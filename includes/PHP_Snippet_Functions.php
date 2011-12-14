<?php
/**
 * Library of static functions used by this plugin.
 
 Data Structure:
 
 Array(
 	[shortname] => Array(
 		[file] => /full/path/to/shortname.snippet.php
 		[description] => An optional description for the Snippet
 		[controller] => /full/path/to/optional/shortname.controller.php
 	),
 )
 
 *
 */
class PHP_Snippet_Functions {

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
	public static $warnings;
	
	/**
	 * We store these to ensure that there are no conflicts when a user creates their own.
	 */
	public static $existing_shortcodes = array();
	
	
	/**
	 * All snippets
	 */
	public static $snippets = array();


	public static function add_menu() {
		add_options_page('PHP Snippets', 'PHP Snippets', 'manage_options', 'php-snippets', 'PHP_Snippet_Functions::settings');
	}

	/**
	 * Create custom post-type menu
	 */
	public static function create_admin_menu()
	 {
	 	add_options_page( 
	 		'PHP Snippets', 					// page title
	 		'PHP Snippets', 					// menu title
			'manage_options', 					// capability
	 		'php_snippets', 					// menu slug
	 		'PHP_Snippet_Functions::get_admin_page' // callback	 	
	 	);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Given a tag, e.g. 'my-code', this will return the full filename for that tag.
	 * If the tag isn't registered, it forces a re-scan of the directory.
	 *
	 * @param	string	$tag basename of the file/shortcode name
	 * @param	string	full path to file OR a false on errors.
	 */
	public static function get_filename_from_tag($tag) {
		if (!isset(self::$data['snippets'][$tag])) {
			self::get_snippets(true); // re-scan
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * This is what populates our "radar" -- finds all available Snippets.  Used 
	 * by the PHP_Snippet constructor when adding shortcodes and by the Ajax controller
	 * list_snippets.php.
	 *
	 * Optionally forces a re-scan of the directory, otherwise reads from cache.
	 *
	 * Populates 
	 *
	 * @param	boolean	$force_scan if true, the directories will be re-scanned, otherwise, cached data is used.
	 */
	public static function get_snippets($force_scan=false) {
		//if ($force_scan) {
		self::$data = get_option(self::db_key, array());

		//}
		// Scan built-in directory
		$dirs[] = PHP_SNIPPETS_PATH .'/snippets';
		
		$user_dir = self::get_value(self::$data, 'snippet_dir', '');
		if (!empty($user_dir)){
			$dirs[] = $user_dir;
		}
		
		foreach($dirs as $dir){			
			$rawfiles = scandir($dir);
			foreach ($rawfiles as $f) {
				// Check immediate sub-dirs
				if (is_dir($dir.'/'.$f)) { 
					$raw_subfiles = scandir($dir.'/'.$f);
					foreach ($raw_subfiles as $sf) {
						if ( !preg_match('/^\./', $sf) && preg_match('/\.snippet\.php$/', $sf) ) {
							$shortname = basename($sf);
							$shortname = preg_replace('/\.snippet\.php$/', '', $shortname);
							$path = $dir.'/'.$sf; // store the path to snippet
							self::$snippets[$shortname] = self::get_snippet_info($path);
						}				
					}
				}
				else {
					if ( !preg_match('/^\./', $f) && preg_match('/\.snippet\.php$/', $f) ) {
						$shortname = basename($f);
						$shortname = preg_replace('/\.snippet\.php$/', '', $shortname);
						$path = $dir.'/'.$f; // store the path to snippet
						self::$snippets[$shortname] = self::get_snippet_info($path);
					}			
				}
			}
		}
		
		return self::$snippets;
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
	 * Reads from settings.
	 */
	public static function get_snippet_dir() {
		$dir = plugin_dir_path(dirname(__FILE__)) . 'snippets';
		if (isset($data['snippet_dir'])) {
			if (!file_exists($data['snippet_dir'])) {
				// throw error!
				self::register_warning( __('The selected Snippet directory does not exist!', 'php_snippets'));
				return $dir;
			}
			if (!is_dir($data['snippet_dir'])) {
				
			}
		}
		return $dir;
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
			wp_enqueue_script('php_snippets_manager', PHP_SNIPPETS_URL . '/js/manager.js' );
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
		add_action('admin_menu', 'PHP_Snippet_Functions::add_menu');
		
		self::$Snippet = new PHP_Snippet();
		self::$Ajax = new PHP_Ajax();
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
		self::$warnings[] = $msg;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Generate the settings page
	 */
	public static function settings() {
		include(PHP_SNIPPETS_PATH.'/controllers/settings.php');
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Adds the button to the TinyMCE 1st row.
	 */
	public static function tinyplugin_add_button($buttons) {
	    array_push($buttons, '|', 'php_snippets');
	    return $buttons;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public static function tinyplugin_register($plugin_array) {
	    $url = PHP_SNIPPETS_URL.'/js/editor_plugin.js';
	    $plugin_array['php_snippets'] = $url;
/*		$fp = fopen('/tmp/wp.txt', 'a');
		fwrite($fp, print_r($plugin_array, true));
		fclose($fp);
*/
	    return $plugin_array;
	}


}
/*EOF*/