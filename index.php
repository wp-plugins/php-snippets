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
function php_snippets_preflight() {

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

add_action('admin_notices', 'php_snippets_preflight');


// All clear?  Fire the missiles.
if (empty($php_snippet_errors)) include_once dirname(__FILE__).'/loader.php';

/*EOF*/