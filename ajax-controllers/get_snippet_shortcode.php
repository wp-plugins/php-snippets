<?php
/*------------------------------------------------------------------------------
Fires when a user makes a selection in the PHP Snippets widget:
Given a filepath, this returns the shortcode req'd to execute it
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

if (!isset($_POST['snippet_path'])) {
	die('Missing snippet_path');
}
elseif (empty($_POST['snippet_path'])) {
	return '';
}

$info = PhpSnippets\Base::get_snippet_info($_POST['snippet_path']); 

print $info['shortcode'];

/*EOF*/