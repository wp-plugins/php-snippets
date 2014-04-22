<?php

//------------------------------------------------------------------------------
/**
 * This class handles Ajax requests. The problem here is that WP requires that 
 * actions be hard-coded, and you can't add arguments to them.  So what we do
 * here is on instatiation, this class dynamically creates action-handlers 
 * for each controller file inside of the ajax-controllers directory, and 
 * this class does nonce-checking in order to make the ajax controllers 
 * as simplified as possible.
 *
 * This class is designed to be a pass-thru, so any Ajax request
 * gets routed here to the appropriate Ajax controller. Ajax calls should be
 * made like this:
 *
 * <script>
 * jQuery.post(
 * 		php_snippets.ajax_url,
 * 		{
 * 			action : 'my_ajax_controller',
 * 			my_ajax_controller_nonce : cctm.ajax_nonce
 * 			// additional variables here
 * 		},
 * 		function( response ) {
 * 			// ... do something ...
 * 		}
 * );
 * </script>
 *
 * @package phpsnippets
 */
namespace PhpSnippets;

class Ajax {

	/**
	 * Contains key value pairs where key = basename of controller (no .php extension)
	 * and value is full path to the controller file.
	 */
	public static $controllers = array();

	//------------------------------------------------------------------------------
	/**
	 * Nonces exist in the $_POST array using the key named like this:
	 * conroller_name + _nonce.  The nonce is always named "ajax_nonce".
	 * WARNING: The response returned by the ajax-controllers *must* be wrapped in
	 * some kind of HTML tag, otherwise you can't use jQuery('#target_id').html(x)
	 * to write it.
	 *
	 * @param string $name
	 * @param mixed $args
	 */
	public static function __callStatic($name, $args) {
		if (!isset(self::$controllers[$name])) {
			die(sprintf(__('Invalid Ajax controller: %s', 'php_snippets'), "<em>$name</em>"));
		}

		// The nonce here should line up with the one localized in the loader.php
		$nonce = '';
		if (isset($_REQUEST[$name.'_nonce'])) {
			$nonce = $_REQUEST[$name.'_nonce'];
		}

		if ( ! wp_verify_nonce( $nonce, 'php_snippets_nonce' ) ) {
			die(sprintf(__('Invalid nonce for %s', 'php_snippets'), "<em>$name</em>"));
		}

		include self::$controllers[$name];

		exit;
	}


	//------------------------------------------------------------------------------
	/**
	 * The construct: here we add "listeners" to any defined Ajax event.  Each Ajax
	 * controller has its own event (i.e. action).
	 */
/*
	public function __construct() {

		// Scan directory
		$dir = PHP_SNIPPETS_PATH .'/ajax-controllers';
		$rawfiles = scandir($dir);
		foreach ($rawfiles as $f) {
			if ( !preg_match('/^\./', $f) && preg_match('/\.php$/', $f) ) {
				$shortname = basename($f);
				$shortname = preg_replace('/\.php$/', '', $shortname);
				$this->controllers[$shortname] = $dir.'/'.$f;
			}
		}
		// TODO: scan 3rd party directory
		
		foreach ($this->controllers as $shortname => $path) {
			add_action( 'wp_ajax_'.$shortname, array($this, $shortname) );
		}
	}
*/
}

/*EOF*/