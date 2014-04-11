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

    public function testget_snippets() {

        $snippets = Phpsnippets\Functions::get_snippets(dirname(__FILE__).'/dir1', '.snippet.php');
        $this->assertTrue(count($snippets) == 3 , 'There are 3 .snippet.php files in dir1');
        $snippets = Phpsnippets\Functions::get_snippets(dirname(__FILE__).'/dir1', '.php');
        $this->assertTrue(count($snippets) == 6 , 'There are 6 .php files in dir1');

    }
}