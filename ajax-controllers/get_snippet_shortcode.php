<?php
/*------------------------------------------------------------------------------
Fires when a user selection for the PHP Snippets widget:
Given a filepath, this returns the shortcode for that widget
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

if (!isset($_POST['snippet_path'])) {
	die('Missing snippet_path');
}
elseif (empty($_POST['snippet_path'])) {
	return '';
}

$info = PHP_Snippet_Functions::get_snippet_info($_POST['snippet_path']); 

print $info['shortcode'];

/*EOF*/