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
class Functions {

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
	 * We store these to ensure that there are no conflicts when a user creates their own.
	 */
	public static $existing_shortcodes = array();
	
	
	/**
	 * All snippets
	 */
	public static $snippets = array();

	/**
	 * All directories
	 */
	public static $directories = array();



	public static function add_menu() {
		add_options_page('PHP Snippets', 'PHP Snippets', 'manage_options', 'php-snippets', 'PhpSnippets\Functions::settings');
	}


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
	 * Create custom post-type menu
	 */
	public static function create_admin_menu() {
	 	add_options_page( 
	 		'PHP Snippets', 					// page title
	 		'PHP Snippets', 					// menu title
			'manage_options', 					// capability
	 		'php_snippets', 					// menu slug
	 		'PhpSnippets\Functions::get_admin_page' // callback	 	
	 	);
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
			$info['shortcode'] 	= '';
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
	 * Used to initialize the plugin
	 */
	public static function init() {
		
		if (is_admin()) {		
			//wp_enqueue_script('media-upload'); // We need the send_to_editor() function.
			wp_enqueue_script('jquery');
			// thickbox must be loaded for firefox
			wp_enqueue_style('thickbox');
 			wp_enqueue_script('thickbox');

			wp_enqueue_script('php_snippets_manager', PHP_SNIPPETS_URL . '/js/manager.js','','4.0.0' );
			wp_register_style('php_snippets_css', PHP_SNIPPETS_URL . '/css/style.css');
			wp_enqueue_style('php_snippets_css');
		}
		
		// The following makes PHP variables available to Javascript the "correct" way.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=226
		$data = array();
		$data['url'] = PHP_SNIPPETS_URL;
		$data['ajax_url'] = admin_url( 'admin-ajax.php' );
		$data['ajax_nonce'] = wp_create_nonce('php_snippets_nonce');
		// Make sure your 2nd argument is not the name of an existing JS function!
		wp_localize_script( 'php_snippets_manager', 'php_snippets', $data );
	
			
		//load_plugin_textdomain( 'php_snippets', false, PHP_SNIPPETS_PATH.'/lang/' );
		//self::$data = get_option('php_snippets_data', array());
		global $shortcode_tags;
		self::$existing_shortcodes = array_keys($shortcode_tags);
		//print '<pre>'; print_r($shortcode_tags); print '</pre>'; exit;
		
		// Add a menu item
		add_action('admin_menu', 'PhpSnippets\Functions::add_menu');
		
		self::$Snippet = new Snippet();
		self::$Ajax = new Ajax();
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
	
	//------------------------------------------------------------------------------
	/**
	 * Generate the settings page
	 */
	public static function settings() {
		include PHP_SNIPPETS_PATH.'/controllers/settings.php';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Adds the button to the TinyMCE 1st row.
	 * @param array $buttons
	 * @return array
	 */
	public static function tinyplugin_add_button($buttons) {
	    array_push($buttons, '|', 'php_snippets');
	    return $buttons;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * @param array $plugin_array
	 * @return array
	 */
	public static function tinyplugin_register($plugin_array) {
	    $url = PHP_SNIPPETS_URL.'/js/editor_plugin.js';
	    $plugin_array['php_snippets'] = $url;
	    return $plugin_array;
	}
}
/*EOF*/