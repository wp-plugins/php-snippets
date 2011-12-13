<?php
//if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Define settings for the PHP Snippets plugin. 
------------------------------------------------------------------------------*/

// Page variables
$data = array();
$data['page_title'] = 'Settings';
$data['content']	= 'This is my content.';
$data['help'] = 'http://code.google.com/p/wordpress-php-snippets/';
$data['msg'] = '';
$data['menu'] = '';
$data['submit'] = __('Save', 'php_snippets');
$data['action_name']  = 'php_snippets_settings';
$data['nonce_name']  = 'php_snippets_settings_nonce';



$data['value'] = self::get_value(self::$data, 'snippet_dir');

// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// A little cleanup before we handoff to save_definition_filter
	$snippet_dir = trim(strip_tags(self::get_value($_POST, 'snippet_dir')));
	if (empty($snippet_dir)) {
		$data['msg'] = sprintf('<div class="error"><p>%s</p></div>', 'Directory cannot be empty.');	
	}
	elseif (!is_dir($snippet_dir)){
		$data['msg'] = sprintf('<div class="error"><p>%s %s</p></div>', 'Could not read directory', "<code>$snippet_dir</code>");
	}
	else {
		$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>', 'Your settings have been updated!');
		self::$data['snippet_dir'] = $snippet_dir;
		update_option(self::db_key, self::$data);
		$data['value'] = $snippet_dir;
	}
}

$data['content'] = self::load_view('settings.php', $data);

print self::load_view('default.php', $data);
/*EOF*/