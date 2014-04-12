<?php
// Working directly with wordpress fails. See https://github.com/sebastianbergmann/phpunit/issues/451
//require_once dirname(__FILE__) . '/../../../../wp-config.php';
require_once dirname(dirname(__FILE__)).'/includes/Base.php';
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
	 * 
	 */
    public function testGetSnippetsFromDirectory() {
        $snippets = Phpsnippets\Base::get_snippets(dirname(__FILE__).'/dir1', '.snippet.php');
        $this->assertTrue(count($snippets) == 4 , 'There are 4 .snippet.php files in dir1');
        $snippets = Phpsnippets\Base::get_snippets(dirname(__FILE__).'/dir1', '.php');
        $this->assertTrue(count($snippets) == 7 , 'There are 7 .php files in dir1');

    }

    /**
	 * 
	 */
    public function testGetDirs() {
        $data = array(dirname(__FILE__).'/dir1',dirname(__FILE__).'/dir2','test');
        $dirs = Phpsnippets\Base::get_dirs($data,0);
        $this->assertTrue(count(array_filter($dirs)) == 2 , 'There are 2 Valid dirs');
    }


    /**
     * 
     */
    public function testGetSnippetInfo() {
    	$info = Phpsnippets\Base::get_snippet_info(dirname(__FILE__).'/dir1/time_delay.snippet.php');
    	$this->assertTrue(is_array($info), 'Snippet is Valid.');
    }

    /**
     *
     */
    public function testBadSyntaxRecognition() {
        // Keep bad syntax named as ".off" so it won't be executed by accident.
        // This avoids the SVN pre-commit hook problem -- it won't let you commit bad code.
    	$result = Phpsnippets\Base::has_bad_syntax(dirname(__FILE__).'/dir2/badsyntax.php.off');
    	$this->assertTrue($result !== false, 'The badsyntax.php snippet should have been recognized as having bad syntax.');
    	$result = Phpsnippets\Base::has_bad_syntax(dirname(__FILE__).'/dir2/goodsyntax.php');
    	$this->assertTrue($result === false, 'The goodsyntax.php snippet should have been recognized as having good syntax.');
    }

    /**
     *
     */
    public function testParseDocblock() {
    	$result = Phpsnippets\Base::get_snippet_info(dirname(__FILE__).'/dir2/docblock1.php');
    	$this->assertTrue($result['desc'] == 'This is my whacky description', 'The description was not detected properly.');
        $this->assertTrue(count($result['params']) == 2, '2 parameters were defined');
    	$this->assertTrue($result['params']['y'] == 123, 'The default parameter was not detected properly.');
    }
    
    /**
     *
     *
     */
    public function testShortcodeGeneration() {
        $info = array(
            'shortcode' => '[myshortcode x="1"]'
        );
        $result = Phpsnippets\Base::get_shortcode($info,'myshortcode');
    	$this->assertTrue($result == $info['shortcode'], 'The shortcode was not generated properly.');

        $info = array(
            'content' => ''
        );
        $result = Phpsnippets\Base::get_shortcode($info,'myshortcode');
    	$this->assertTrue($result == '[myshortcode][/myshortcode]', 'The shortcode was not generated properly.');

        $info = array(
            'params' => array(
                'x' => '',
                'y' => '456'
            )
        );
        $result = Phpsnippets\Base::get_shortcode($info,'myshortcode');
    	$this->assertTrue($result == '[myshortcode x="" y="456"]', 'The shortcode was not generated properly.');

    }
}