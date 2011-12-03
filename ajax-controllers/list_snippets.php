<?php
/*------------------------------------------------------------------------------
Fires when a user clicks the button in the TinyMCE editor.
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

// Template variables
$data = array();
$data['pagetitle'] = __('Select a Snippet', 'php_snippets');
$data['content'] = '';
$data['other_shortcodes'] = '';

$snippets = PHP_Snippet_Functions::get_snippets(); 


foreach($snippets as $shortname => $path) {
	$data['content'] .= '<strong class="linklike" onclick="javascript:insert_shortcode(\'['.$shortname.']\');">'.$shortname.'</strong><br/>';
}

/*
foreach (PHP_Snippet_Functions::$existing_shortcodes as $shortname) {
	$data['other_shortcodes'] = '<strong class="linklike" onclick="javascript:insert_shortcode(\'['.$shortname.']\');">'.$shortname.'</strong><br/>';
}
*/

// Use us a template
print PHP_Snippet_Functions::load_view('thickbox.php', $data);

/*EOF*/