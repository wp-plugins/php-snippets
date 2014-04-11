<?php
if (!defined('WP_PLUGIN_DIR')) exit('No direct script access allowed');
/*
Plugin Name: PHP Snippets
Plugin URI: http://code.google.com/p/wordpress-php-snippets/
Description: Publishes shortcodes for each PHP file contained in a specified directory.
Author: Everett Griffiths
Version: 0.9
Author URI: http://craftsmancoding.com/
*/

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
function php_snippets_print_notices() {

	global $php_snippet_errors;

    $php_license = PhpSnippets\License::check();
    // Don't show this when we're posting data
    if($php_license != 'valid' && empty($_POST)) {
        print PhpSnippets\License::get_error_msg();
    }
	
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

//------------------------------------------------------------------------------
//! Load up!
//  plugin_dir_path(__FILE__)
//------------------------------------------------------------------------------
define('PHP_SNIPPETS_PATH', dirname(__FILE__) );
define('PHP_SNIPPETS_URL', WP_PLUGIN_URL .'/'. basename( PHP_SNIPPETS_PATH ) );
require_once PHP_SNIPPETS_PATH . '/includes/Functions.php';
require_once PHP_SNIPPETS_PATH . '/includes/License.php';

add_action('admin_notices', 'php_snippets_print_notices');

if (empty($php_snippet_errors)) {
    require_once PHP_SNIPPETS_PATH . '/includes/Snippet.php';
    require_once PHP_SNIPPETS_PATH . '/includes/Ajax.php';
    require_once PHP_SNIPPETS_PATH . '/includes/Widget.php';
    
    add_filter('mce_external_plugins', 'PhpSnippets\Functions::tinyplugin_register');
    add_filter('mce_buttons', 'PhpSnippets\Functions::tinyplugin_add_button', 0);
    add_action('init','PhpSnippets\Functions::init');
    add_action('widgets_init', 'PhpSnippets\Widget::register_this_widget');
   
}




/*EOF*/