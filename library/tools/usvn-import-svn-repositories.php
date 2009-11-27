#!/usr/bin/env php
<?php
/**
 * Import SVN repositories
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7.0
 * @package utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: usvn-import-svn-repositories.php 632 2007-10-17 15:51:08Z dolean_j $
 */

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../app'));
if (!defined('USVN_BASE_DIR'))
  define('USVN_BASE_DIR', realpath(dirname(__FILE__) . '/../../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('USVN_');
$autoloader->setFallbackAutoloader(true);

/**
 * Get options and directories paths to check
 */
$results = array();
$repos = array();
$paths = array();
$configFile = '';
$usage = "Usage: $argv[0] [--recursive] [--verbose] [--creategroup] [--addmetogroup] [--admin] [--noimport] [login] [config-file] path1 path2 path3 [...]\n";
if (count($argv) == 1) {
	print($usage);
	exit(1);
}

$options = array('recursive' => false,
					'verbose' => false,
					'login' => 0,
					'creategroup' => 0,
					'addmetogroup' => 0,
					'admin' => 0,
					'noimport' => 0);

foreach ($argv as $arg) {
	switch ($arg) {
		case '--verbose':
			$options['verbose'] = true;
			break;
		case '--recursive':
			$options['recursive'] = true;
			break;
		case '--creategroup':
			$options['creategroup'] = 1;
			break;
		case '--addmetogroup':
			$options['addmetogroup'] = 1;
			break;
		case '--admin':
			$options['admin'] = 1;
			break;
		case '--noimport':
			$options['noimport'] = 1;
			break;
		default:
			if (is_file($arg) && $arg != $argv[0]) {
				$configFile = $arg;
				break;
			}
			if (is_dir($arg)) {
				$paths[] = $arg;
			} elseif (preg_match('/^--/', $arg)) {
				print("'$arg' option unknown\n");
				exit(1);
			} elseif ($arg != $argv[0] && preg_match('/\w+/', $arg)) {
				$options['login'] = $arg;
			} else {
				if ($arg != $argv[0]) {
					print($usage);
					exit(1);
				}
			}
			break;
	}
}

/**
 * Initialize some configurations details
 */
try {
	$configFile = !empty($configFile) ? $configFile : 'config.ini';
	$config = new USVN_Config_Ini($configFile, "general");
	if ($options['verbose']) {
		print "Config file loaded: $configFile\n";
	}
	Zend_Registry::set('config', $config);

	USVN_Translation::initTranslation($config->translation->locale, APPLICATION_PATH . "/locale");
	date_default_timezone_set($config->timezone);

	$db = Zend_Db::factory($config->database->adapterName, $config->database->options->toArray());
	USVN_Db_Table::$prefix = $config->database->prefix;
	Zend_Db_Table::setDefaultAdapter($db);

	Zend_Registry::set('config', $config);
}
catch (Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}

/**
 * Look for SVN repositories to import into USVN and finally perform the action
 */

$svnImport = new USVN_ImportSVNRepositories();
foreach ($paths as $path) {
	$tmp = $svnImport->lookAfterSVNRepositoriesToImport($path, $options);
	if (count($tmp)) {
		$repos = array_merge($repos, $tmp);
	} else {
		if ($options['verbose']) {
			print "No SVN repository here: $path\n";
		}
	}
}

//if we don't have any repository to import then die
if (!count($repos)) {
	print($usage);
	exit(1);
}

/**
 * Now we can import (if there is no option 'noimport') our SVN Repositories into USVN
 */
$svnImport->addSVNRepositoriesToImport($repos, $options);
if (!$options['noimport']) {
	$results = $svnImport->importSVNRepositories();
	print "SVN repositories imported:\n".implode("\n", $repos);
} else {
	print "SVN repositories ready to import:\n".implode("\n", $repos);
}

exit(0);