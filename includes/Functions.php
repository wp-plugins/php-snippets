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
	 * Stores Plugin settings and data.
	 */
	public static $data;
	
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
			$dir = self::parse_dirname($dir);
			if (!file_exists($dir)) {
				// throw error!
				self::register_warning(sprintf(__('Directory does not exist! %s', 'php_snippets'), "<code>$dir</code>"));
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
	

	//------------------------------------------------------------------------------
	/**
	 * This is what populates our "radar" -- finds all available Snippets.  Used 
	 * by the PHP_Snippet constructor when adding shortcodes and by the Ajax controller
	 * list_snippets.php.
	 *
	 *
	 * Populates 
	 *
	 * @param	boolean	$force_scan if true, the directories will be re-scanned, otherwise, 
	 *					cached data is used.
	 * @return	array. shortname => Info
	 *			On errors, an empty array is returned and warnings are registered.
	 */
	public static function get_snippets($force_scan=false) {
		//if ($force_scan) {
		self::$data = get_option(self::db_key, array());
		$show_builtin_snippets = self::get_value(self::$data, 'show_builtin_snippets', '');
		//}
		// Scan built-in directory
		$dirs = self::get_value(self::$data, 'snippet_dirs', array());
		
		if($show_builtin_snippets) {
			$dirs[] = PHP_SNIPPETS_PATH .'/snippets';
		}
		
		$suffix = self::get_value(self::$data, 'snippet_suffix','.snippet.php');
		

	
		foreach($dirs as $dir){

			if (!self::check_permissions($dir)) continue;
			$dir = self::parse_dirname($dir);
			$rawfiles = @scandir($dir);
			
			foreach ($rawfiles as $f) {

				// Check immediate sub-dirs
				if (is_dir($dir.'/'.$f)) { 
					$raw_subfiles = scandir($dir.'/'.$f);
					
					
					foreach ($raw_subfiles as $subfile) {

						if ( !preg_match('/^\./', $subfile) && strpos($subfile, $suffix) ) {
							$shortname = basename($subfile);
							$shortname = str_replace($suffix, '', $shortname);
							$path = $dir.'/'.$f.'/'.$subfile; // store the path to snippet
							self::$snippets[$shortname] = self::get_snippet_info($path);
						}				
					}
				}
				// Or check files inside the main snippet directory
				//else {
					if ( !preg_match('/^\./', $f) && strpos($f, $suffix) ) {

						$shortname = basename($f);
						$shortname = str_replace($suffix, '', $shortname);
						$path = $dir.'/'.$f; // store the path to snippet

						self::$snippets[$shortname] = self::get_snippet_info($path);
					}			
				//}
			}

		}
		//die(print_r(self::$snippets,true));
		/*echo '<pre>';
		print_r(self::$snippets);
		die();*/
		return self::$snippets;
	}

	//------------------------------------------------------------------------------
	/**
	 * This is what populates our "radar" -- finds all available Snippets.  Used 
	 * by the PHP_Snippet constructor when adding shortcodes and by the Ajax controller
	 * list_snippets.php.
	 *
	 *
	 * Populates 
	 * @return	array. shortname => Info
	 *			On errors, an empty array is returned and warnings are registered.
	 */
	public static function get_snippets2($dir='') {

		$snippets = array();
		self::$data = get_option(self::db_key, array());		
		$suffix = self::get_value(self::$data, 'snippet_suffix','.snippet.php');
		

		$dir = self::parse_dirname($dir);
		$rawfiles = @scandir($dir); 
		unset($rawfiles[0]);
		unset($rawfiles[1]);

		if(!empty($rawfiles)) {
			foreach ($rawfiles as $f) {
				if(!is_dir($dir.'/'.$f)) {
					if ( !preg_match('/^\./', $f) && strpos($f, $suffix) ) {
						$shortname = basename($f);
						$shortname = str_replace($suffix, '', $shortname);
						$path = $dir.'/'.$f; // store the path to snippet

						$snippets[$shortname] = self::get_snippet_info($path);
					}			
				}
				
			}
		}
			
		return $snippets;
	}

	/**
	 * Get all directories
	 * @return array of directories
	 */
	public static function get_snippet_dirs() {
		self::$data = get_option(self::db_key, array());
		$dirs = self::$data['snippet_dirs'];
		$show_builtin_snippets = self::get_value(self::$data, 'show_builtin_snippets', '');

		
		if($show_builtin_snippets) {
			$dirs[] = PHP_SNIPPETS_PATH .'/snippets';
		}

		foreach($dirs as $dir){

			if (!self::check_permissions($dir)) continue;
			$dir = self::parse_dirname($dir);
		
			$rawfiles = @scandir($dir); 
			unset($rawfiles[0]);
			unset($rawfiles[1]);

			self::$directories[] = $dir;
			foreach ($rawfiles as $f) {
				// Check immediate sub-dirs
				if (is_dir($dir.'/'.$f)) { 
					self::$directories[] = $dir .'/'.$f;
				}
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
	
		$info['path'] 		= $path;
		$info['desc'] 		= '';
		$info['shortcode'] 	= '';
		$info['dir']	= dirname($path);
		
		if (file_exists($path)) {
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