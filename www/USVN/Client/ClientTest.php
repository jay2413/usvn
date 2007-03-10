<?php
// Call USVN_Client_ClientTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
	define("PHPUnit_MAIN_METHOD", "USVN_Client_ClientTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

echo getcwd();

require_once 'www/USVN/autoload.php';
require_once 'USVN/Client/Client.php';

/**
 * Test class for USVN_Client_Client.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-10 at 19:35:59.
 */
class USVN_Client_ClientTest extends PHPUnit_Framework_TestCase {
	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
	public static function main() {
		require_once "PHPUnit/TextUI/TestRunner.php";

		$suite  = new PHPUnit_Framework_TestSuite("USVN_Client_ClientTest");
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
	protected function setUp() {
	}

	/**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
	protected function tearDown() {
	}


	public function test_noArgs()
	{
		try
		{
			$client = new USVN_Client_Client(array());
		}
		catch (Exception $e)
		{
			return;
		}
		$this->fail();
	}

	public function test_commandInvalid()
	{
		try
		{
			$client = new USVN_Client_Client(array("tutu"));
		}
		catch (Exception $e)
		{
			return;
		}
		$this->fail();
	}

	public function test_InvalidArgsMax()
	{
		try
		{
			$client = new USVN_Client_Client(array("version", "tutu"));
		}
		catch (Exception $e)
		{
			return;
		}
		$this->fail();
	}

	public function test_InvalidArgsMin()
	{
		try
		{
			$client = new USVN_Client_Client(array("install", "tutu"));
		}
		catch (Exception $e)
		{
			return;
		}
		$this->fail();
	}
}

// Call USVN_Client_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_Client_ClientTest::main") {
	USVN_Client_ClientTest::main();
}
?>