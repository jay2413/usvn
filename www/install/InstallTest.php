<?php
/**
 * Installation operations
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call InstallTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "InstallTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/install/Install.php';
require_once 'www/USVN/autoload.php';

/**
 * Test class for Install.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-20 at 09:07:00.
 */
class InstallTest extends USVN_Test_Test {
	private $db;

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("InstallTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp() {
		parent::setUp();
    }

	public function testInstallLanguage()
	{
		Install::installLanguage("tests/tmp/config.ini", "fr_FR");
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("fr_FR", $config->translation->locale);
	}

	public function testInstallTimeZone()
	{
		Install::installTimezone("tests/tmp/config.ini", "Europe/Paris");
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("Europe/Paris", $config->timezone);
	}

	public function testInstallCheckForUpdate()
	{
		Install::installCheckForUpdate("tests/tmp/config.ini", true);
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertTrue((bool)$config->update->checkforupdate);
		$this->assertEquals(0, $config->update->lastcheckforupdate);
	}

	public function testInstallLocale()
	{
		Install::installLocale("tests/tmp/config.ini");
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		if (PHP_OS == "Linux") {
			$this->assertContains(".utf8", $config->system->locale);
		} else {
			$this->assertContains('.UTF-8', $config->system->locale);
		}
	}

	public function testInstallSubversion()
	{
		Install::installSubversion("tests/tmp/config.ini", "tests", "tests" . DIRECTORY_SEPARATOR . "htpasswd", "tests" . DIRECTORY_SEPARATOR . "authz", "http://test.com");
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$this->assertTrue(file_exists("tests/authz"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("tests" . DIRECTORY_SEPARATOR, $config->subversion->path);
		$this->assertEquals("tests" . DIRECTORY_SEPARATOR . "htpasswd", $config->subversion->passwd);
		$this->assertEquals("tests" . DIRECTORY_SEPARATOR . "authz", $config->subversion->authz);
		$this->assertEquals("http://test.com/", $config->subversion->url);
	}

	public function testInstallSubversionPathDoesntExist()
	{
		try	{
		Install::installSubversion("tests/tmp/config.ini", "test2", "test2/htpasswd", "test2/authz", 'http://test.com');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function testInstallSubversionMagicQuoteWindows()
	{
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            mkdir('tests/tmp2');
            Install::installSubversion("tests/tmp2/config.ini", 'tests\\\\tmp2', "tests\\\\htpasswd", "tests\\\\authz", 'http://test.com');
            $this->assertTrue(file_exists("tests/tmp2/config.ini"));
            $this->assertTrue(file_exists("tests/authz"));
            $config = new Zend_Config_Ini("tests/tmp2/config.ini", "general");
            $this->assertEquals("tests\\tmp2\\", $config->subversion->path);
        }
	}

	public function testInstallBadLanguage()
	{
		try {
			Install::installLanguage("tests/tmp/config.ini", "fake");
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function testInstallUrl()
	{
		Install::installUrl("tests/tmp/config.ini", "tests/tmp/.htaccess", "/test/install/index.php?step=7", "localhost", false);
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$this->assertTrue(file_exists("tests/tmp/.htaccess"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("/test", $config->url->base);
		$htaccess = file_get_contents("tests/tmp/.htaccess");
		$this->assertContains("RewriteBase /test", $htaccess);
	}

	public function testInstallUrlWithoutinstall()
	{
		Install::installUrl("tests/tmp/config.ini", "tests/tmp/.htaccess", "/test/", "localhost", false);
		$this->assertTrue(file_exists("tests/tmp/config.ini"));
		$this->assertTrue(file_exists("tests/tmp/.htaccess"));
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("/test", $config->url->base);
		$htaccess = file_get_contents("tests/tmp/.htaccess");
		$this->assertContains("RewriteBase /test", $htaccess);
	}

	public function testInstallUrlRoot()
	{
		Install::installUrl("tests/tmp/config.ini", "tests/tmp/.htaccess", "/install/index.php?step=7", "localhost", false);
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("", $config->url->base);
		$htaccess = file_get_contents("tests/tmp/.htaccess");
		$this->assertContains("RewriteBase /", $htaccess);
	}

	public function testInstallUrlCantWriteHtaccess()
	{
		try {
			Install::installUrl("tests/tmp/config.ini", "tests/fake/.htaccess", "/test/install/index.php?step=7", "localhost", false);
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function testInstallUrlCantWriteConfig()
	{
		try {
			Install::installUrl("tests/fake/config.ini", "tests/tmp/.htaccess", "/test/install/index.php?step=7", "localhost", false);
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function testInstallEnd()
	{
		Install::installEnd("tests/tmp/config.ini");
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("0.7", $config->version);
	}

	public function testInstallPossibleNoConfigFile()
	{
		$this->assertTrue(Install::installPossible("tests/tmp/config.ini"));
	}

	public function testInstallPossibleInstallNotEnd()
	{
		Install::installLanguage("tests/tmp/config.ini", "fr_FR");
		$this->assertTrue(Install::installPossible("tests/tmp/config.ini"));
	}

	public function testInstallNotPossible()
	{
		Install::installEnd("tests/tmp/config.ini");
		$this->assertFalse(Install::installPossible("tests/tmp/config.ini"));
	}

	public function testInstallConfiguration()
	{
		Install::installConfiguration("tests/tmp/config.ini", "Noplay");
		$config = new Zend_Config_Ini("tests/tmp/config.ini", "general");
		$this->assertEquals("Noplay", $config->site->title);
		$this->assertEquals("default", $config->template->name);
		$this->assertEquals("medias/default/images/USVN.ico", $config->site->ico);
		$this->assertEquals("medias/default/images/USVN-logo.png", $config->site->logo);
	}

	public function testInstallConfigurationNotTitle()
	{
		try {
			Install::installConfiguration("tests/tmp/config.ini", "");
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function testGetApacheConfig()
	{
		file_put_contents("tests/tmp/config.ini", "[general]
subversion.path=tests" . DIRECTORY_SEPARATOR . "tmp
subversion.url=http://exemple/dev/usvn/
subversion.authz=tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "authz
subversion.passwd=tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "htpasswd
site.title=USVN
		");
		$test =
"<Location /dev/usvn/>
	ErrorDocument 404 default
	DAV svn
	Require valid-user
	SVNParentPath tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "svn
	SVNListParentPath off
	AuthType Basic
	AuthName \"USVN\"
	AuthUserFile tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "htpasswd
	AuthzSVNAccessFile tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "authz
</Location>
";
		$test = str_replace("\r", "", $test);
		$this->assertEquals($test, Install::getApacheConfig("tests/tmp/config.ini"));
	}

	public function testGetApacheConfigWithSSL()
	{
		file_put_contents("tests/tmp/config.ini", "[general]
subversion.path=tests" . DIRECTORY_SEPARATOR . "tmp
subversion.url=https://exemple/dev/usvn/
subversion.authz=tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "authz
subversion.passwd=tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "htpasswd
site.title=USVN
		");
		$test =
"<Location /dev/usvn/>
	ErrorDocument 404 default
	DAV svn
	SSLRequireSSL
	Require valid-user
	SVNParentPath tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "svn
	SVNListParentPath off
	AuthType Basic
	AuthName \"USVN\"
	AuthUserFile tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "htpasswd
	AuthzSVNAccessFile tests" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "authz
</Location>
";
		$test = str_replace("\r", "", $test);
		$this->assertEquals($test, Install::getApacheConfig("tests/tmp/config.ini"));
	}

	public function testcheckSystem()
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		Install::checkSystem();
	}

	public function testcheckSystemSubversionNotInstall()
	{
		$PATH = getenv('PATH');
		try {
			putenv('PATH=');
			Install::checkSystem();
		}
		catch (USVN_Exception $e) {
			putenv("PATH=$PATH");
			return;
		}
		putenv("PATH=$PATH");
		$this->fail();
	}
}

// Call InstallTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "InstallTest::main") {
    InstallTest::main();
}
?>
