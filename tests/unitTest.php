<?php
//require_once dirname(__FILE__) . '/../../../../wp-config.php';
require_once dirname(dirname(__FILE__)).'/includes/Functions.php';
/**
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/moxycart/core/components/moxycart/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and 
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/moxycart/core/components/moxycart/tests
 *
 * To run just the tests in this file, specify the file:
 *
 *  phpunit tests/autoloadTest.php
 *
 */
 
class unitTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test get_snippets function
	 */
    public function testGet_snippets() {
        $snippets = Phpsnippets\Functions::get_snippets(dirname(__FILE__).'/dir1', '.snippet.php');
        $this->assertTrue(count($snippets) == 4 , 'There are 4 .snippet.php files in dir1');
        $snippets = Phpsnippets\Functions::get_snippets(dirname(__FILE__).'/dir1', '.php');
        $this->assertTrue(count($snippets) == 7 , 'There are 7 .php files in dir1');

    }

    /**
	 * Test get_snippets function
	 */
    public function testGet_dirs() {
    	if ( !defined('ABSPATH') )
		define('ABSPATH',  dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
        $dirs = Phpsnippets\Functions::get_dirs(array(dirname(__FILE__).'/dir1',dirname(__FILE__).'/dir2','test'),0);
        $this->assertTrue(count(array_filter($dirs)) == 2 , 'There are 2 Valid dirs');
    }

    /**
     * test get_snippet_info
     */
    public function testGet_snippet_info() 
    {
    	$info = Phpsnippets\Functions::get_snippet_info(dirname(__FILE__).'\dir1\time_delay.snippet.php');
    	$this->assertTrue( !empty($info), 'Snippet is Valid.');

    }
}