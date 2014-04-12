<?php
/**
 * Library of static functions used by this plugin.
 
 Data Structure for $snippets
 
 Array(
 	[shortname] => Array(
 		[file] => /full/path/to/shortname.snippet.php
 		[description] => An optional description for the Snippet
 		[controller] => /full/path/to/optional/shortname.controller.php
 	),
 )
 
 *
 */
namespace PhpSnippets; 
class Base {

	/**
	 * Contains the Ajax object (PHP_Ajax)
	 */
	public static $Ajax;

	/**
	 * Holds the Snippet object (PHP_Snippet)
	 */
	public static $Snippet;

	
	/**
	 * The key in wp_postmeta
	 */
	const db_key = 'php_snippets';
	
	/**
	 * Used to store warnings that are displayed to admin users.
	 */ 
	public static $warnings = array();
	
	
	/**
	 * All snippets
	 */
	public static $snippets = array();

	/**
	 * All directories
	 */
	public static $directories = array();



	/**
	 * Test out a given directory to make sure it has the correct permissions
	 *
	 * @param	string	full path to directory to check.
	 * @return	boolean	true on success (permissions are ok), false on failure (permissions are borked)
	 */
	public static function check_permissions($dir) {
			if(trim($dir) == '') {
				return;
			}
			$dir = self::parse_dirname($dir);
			if (!file_exists($dir)) {
				// throw error!
				self::register_warning($dir);
				return false;
			}
			if (!is_dir($dir)) {
				self::register_warning(sprintf(__('The selected Snippet directory must be a directory, not a file! %s', 'php_snippets'), "<code>$dir</code>"));
				return false;		
			}

		// Assume that permissions are Ok
		return true;
	}
	
	/**
	 * get snippets on the specified directory
	 * @param string $dir /full/path/to/dir
	 * @param string $ext file extension to match.
	 * @return array simple array containing fullpaths to files
	 */
	public static function get_snippets($dir,$ext) {
		$snippets = array();
		$rawfiles = @scandir($dir); 
		$pattern = '/' . preg_quote($ext) . '$/';
		unset($rawfiles[0]);
		unset($rawfiles[1]);

		if(!empty($rawfiles)) {
			foreach ($rawfiles as $f) {
				if(!is_dir($dir.'/'.$f)) {
					if ( preg_match($pattern, $f) && strpos($f, $ext) ) {
						$shortname = basename($f);
						$shortname = str_replace($ext, '', $shortname);
						$snippets[$shortname] = $dir.'/'.$f;
					}			
				}
				
			}
		}
		return $snippets;
	}

	/**
	 * Get a list of all directories where the user said to look for snippets.
	 * We flag each directory as to whether or not it exists.
	 * @param array $dirs data from database
	 * @param boolean $include_built_in if true, include the built-in snippets dir in the list
	 * @return array associative array of full-path-to-dir => true/false.  True if the dir exits. False if it does not
	 */
	public static function get_dirs($dirs,$include_built_in) {
		if($include_built_in) {
			$dirs[] = PHP_SNIPPETS_PATH .'/snippets';
		}
		if(!empty($dirs)) {
			foreach($dirs as $dir){
				self::$directories[$dir] = self::check_permissions($dir) ;
			}
		}

		return self::$directories;
	}
	
	
	//------------------------------------------------------------------------------
	/**
	 * Given the /full/path/to/snippet.php, read a description out of the header,
	 * and read a sample shortcode.
	 *
	 * @param	string	$path to file
	 * @param	array	
	 */
	public static function get_snippet_info($path) {
		$info = array();
		if (file_exists($path)) {
			$info['path'] 		= $path;
			$info['desc'] 		= '';
			$info['shortcode'] 	= ''; // e.g. [my_snippet] or [hey]you[/hey]
			$info['tag']        = ''; // e.g. my_snippet
			
			$contents = file_get_contents($path);
			
			// Get description
			preg_match('/^Description:(.*)$/m', $contents, $matches);
			
			if (isset($matches[1])) {
				$info['desc'] = $matches[1];
			}

			// Get shortcode
			preg_match('/^Shortcode:(.*)$/m', $contents, $matches);
			
			if (isset($matches[1])) {
				$info['shortcode'] = $matches[1];
			}
		}
		
		return $info;
	}

    /**
     * E.g. convert '/path/to/something.snippet.php' to 'something'
     * This is the tag by which we can identify the snippet.
     * @param string full path to file
     * @param string extension
     */
    public static function get_tag($file,$ext) {
        $file = basename($file);
        $file = preg_replace('/'.preg_quote($ext).'$/i','',$file);
        return $file;
    }

	//------------------------------------------------------------------------------
	/**
	 * Designed to safely retrieve scalar elements out of a hash. Don't use this
	 * if you have a more deeply nested object (e.g. an array of arrays).
	 *
	 * @param array   $hash    an associative array, e.g. array('animal' => 'Cat');
	 * @param string  $key     the key to search for in that array, e.g. 'animal'
	 * @param mixed   $default (optional) : value to return if the value is not set. Default=''
	 * @return mixed
	 */

	public static function get_value($hash, $key, $default='') {
		if ( !isset($hash[$key]) ) {
			return $default;
		}
		else {
			if ( is_array($hash[$key]) ) {
				return $hash[$key];
			}
			else {
				return $hash[$key];
			}
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @return string the parsed contents of that file
	 */
	public static function load_view($filename, $data=array()) {

		$path = PHP_SNIPPETS_PATH . '/templates/';

		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			return ob_get_clean();
		}
		die('View file does not exist: ' .$path.$filename);
	}

	//------------------------------------------------------------------------------
	/**
	 * @param	string	$msg	 the localized error message.
	 */
	public static function register_warning($msg) {
		self::$warnings[$msg] = 1; // ensure warnings are not duplicated
	}

    //------------------------------------------------------------------------------
    /**
     * Parse a directory name -- this supports 
     * @param stirng $dirname
     * @return string $dirname
     */
	public static function parse_dirname($dirname) {
        return str_replace('[+ABSPATH+]', ABSPATH, $dirname);
	}
	
}
/*EOF*/