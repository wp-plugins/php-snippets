<?php
/**
 * This page should only get loaded if the site is running php 5.3 or greater,
 * otherwise this file will generate PHP parsing errors.
 *
 */
if (!defined('WP_PLUGIN_DIR')) exit('No direct script access allowed');

//------------------------------------------------------------------------------
//! Load up!
//  plugin_dir_path(__FILE__)
//------------------------------------------------------------------------------
require_once PHP_SNIPPETS_PATH . '/includes/Base.php';
require_once PHP_SNIPPETS_PATH . '/includes/License.php';

$php_license = PhpSnippets\License::check();

// Check the license, show an error
add_action('admin_notices', function(){
    global $php_license;
    // Don't show this when we're posting data
    if($php_license != 'valid' && empty($_POST)) {
        print PhpSnippets\License::get_error_msg();
    }
});

if ($php_license != 'valid') return;

require_once PHP_SNIPPETS_PATH . '/includes/Snippet.php';
require_once PHP_SNIPPETS_PATH . '/includes/Ajax.php';
require_once PHP_SNIPPETS_PATH . '/includes/Widget.php';

add_filter('mce_external_plugins', function($plugin_array) {
    $url = PHP_SNIPPETS_URL.'/js/editor_plugin.js';
    $plugin_array['php_snippets'] = $url;
    return $plugin_array;
});

add_filter('mce_buttons', function($buttons) {
    array_push($buttons, '|', 'php_snippets');
    return $buttons;
}, 0);

// Main functionality here:
add_action('init',function(){
	if (is_admin()) {		
		//wp_enqueue_script('media-upload'); // We need the send_to_editor() function.
		wp_enqueue_script('jquery');
		// thickbox must be loaded for firefox
		wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');

		wp_enqueue_script('php_snippets_manager', PHP_SNIPPETS_URL . '/js/manager.js','','4.0.0' );
		wp_register_style('php_snippets_css', PHP_SNIPPETS_URL . '/css/style.css');
		wp_enqueue_style('php_snippets_css');

        // The following makes PHP variables available to Javascript the "correct" way.
        // See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=226
        $data = array();
        $data['url'] = PHP_SNIPPETS_URL;
        $data['ajax_url'] = admin_url( 'admin-ajax.php' );
        $data['ajax_nonce'] = wp_create_nonce('php_snippets_nonce');
        // Make sure your 2nd argument is not the name of an existing JS function!
        wp_localize_script( 'php_snippets_manager', 'php_snippets', $data );

		// We only need the additional functionality for the back-end.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=331
		if('widgets.php' == substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1)) {
			wp_enqueue_script('thickbox');
			wp_register_script('PHP_Snippet_Widget', PHP_SNIPPETS_URL.'/js/widget.js', array('jquery'));
			wp_enqueue_script('PHP_Snippet_Widget');
		}
	}

    // TODO
	//load_plugin_textdomain( 'php_snippets', false, PHP_SNIPPETS_PATH.'/lang/' );
	global $shortcode_tags;
	$existing_shortcodes = array_keys($shortcode_tags);
			
    // Load up data
    $ps_data = get_option(Phpsnippets\Base::db_key, array());
    $defined_dirs = Phpsnippets\Base::get_value($ps_data, 'snippet_dirs', array());
    $ext = Phpsnippets\Base::get_value($ps_data, 'snippet_suffix', '.php');
    $include_built_in = Phpsnippets\Base::get_value($ps_data, 'show_builtin_snippets', true);
    
    // Set any placeholders we want to support in directory names
    Phpsnippets\Base::set_placeholder('ABSPATH', ABSPATH);

    // Get all snippets in all dirs
    $dirs = Phpsnippets\Base::get_dirs($defined_dirs,$include_built_in);
    PhpSnippets\Widget::setDirs($dirs);
    PhpSnippets\Widget::setExt($ext);
    // Loop thru each dir
    foreach ($dirs as $d => $d_exists) {
        $snippets = (array) Phpsnippets\Base::get_snippets($d,$ext);
        // Loop thru each file
    	foreach ($snippets as $s) {
            Phpsnippets\Base::add_shortcode($s,$ext);
    	}    
    }
	
    // Register Ajax Calls
	Phpsnippets\Ajax::$controllers['dir_snippets'] = PHP_SNIPPETS_PATH .'/ajax-controllers/dir_snippets.php';
	Phpsnippets\Ajax::$controllers['get_snippet_shortcode'] = PHP_SNIPPETS_PATH .'/ajax-controllers/get_snippet_shortcode.php';
	Phpsnippets\Ajax::$controllers['list_snippets'] = PHP_SNIPPETS_PATH .'/ajax-controllers/list_snippets.php';
	foreach (Phpsnippets\Ajax::$controllers as $shortname => $path) {
		add_action( 'wp_ajax_'.$shortname, 'Phpsnippets\Ajax::'.$shortname);
	}
});

add_action('widgets_init', 'PhpSnippets\Widget::register_this_widget');

/*EOF*/