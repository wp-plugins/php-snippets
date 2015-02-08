<?php
if (!defined('WP_PLUGIN_DIR')) exit('No direct script access allowed');
/*
Plugin Name: PHP Snippets
Plugin URI: http://code.google.com/p/wordpress-php-snippets/
Description: Publishes shortcodes for each PHP file contained in specified directory(ies).
Author: Everett Griffiths
Version: 1.1
Author URI: http://craftsmancoding.com/

The plugin loading is split between index.php and loader.php so we can gracefully handle cases where the user is not
running at least PHP 5.3.
*/
define('PHP_SNIPPETS_PATH', dirname(__FILE__) );
define('PHP_SNIPPETS_URL', WP_PLUGIN_URL .'/'. basename( PHP_SNIPPETS_PATH ) );
//------------------------------------------------------------------------------
//! Tests
//------------------------------------------------------------------------------
$required_php_version = '5.3.0';
$php_snippet_errors = array();

if ( version_compare( phpversion(), $required_php_version, '<') ) {
	$msg = sprintf( __('The PHP Snippets plugin requires PHP %2$s or newer.', 'php_snippets' )
		,  $required_php_version);
	$msg .= __('Talk to your system administrator about upgrading.', 'php_snippets');	
	$php_snippet_errors[] = $msg;
}


//------------------------------------------------------------------------------
//! Functions -- minimal for handling tests
//------------------------------------------------------------------------------
function php_snippets_preflight() {

	global $php_snippet_errors;

	if ( !empty($php_snippet_errors) ) {
		$error_items = '';
		foreach ( $php_snippet_errors as $e ) {
			$error_items .= "<li>$e</li>";
		}
		$msg = __('The PHP Snippets plugin encountered errors! It cannot load!', 'php_snippets');
		printf('<div id="php-snippets-errors" class="error">
			<p>
				<strong>%1$s</strong>
				<ul style="margin-left:30px;">
					%2$s
				</ul>
			</p>
			</div>'
			, $msg
			, $error_items);
	}
}

function php_include_settings_page(){
    include PHP_SNIPPETS_PATH.'/controllers/settings.php';
}

function php_snippets_add_admin_menu() {
    add_options_page('PHP Snippets', 'PHP Snippets', 'manage_options', 
        'php-snippets', 'php_include_settings_page');
}

add_action('admin_notices', 'php_snippets_preflight');

// Add a menu item
add_action('admin_menu', 'php_snippets_add_admin_menu');

// All clear?  Fire the missiles.
if (empty($php_snippet_errors)) include_once dirname(__FILE__).'/loader.php';

/*EOF*/