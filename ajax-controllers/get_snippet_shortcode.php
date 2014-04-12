<?php
/*------------------------------------------------------------------------------
Fires when a user makes a selection in the PHP Snippets widget:
Given a filepath, this returns the shortcode req'd to execute it
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

if (!isset($_POST['snippet_path']) || empty($_POST['snippet_path'])) {
	print 'Missing snippet_path.';
	return;
}

$ps_data = get_option(Phpsnippets\Base::db_key, array());
$ext = Phpsnippets\Base::get_value($ps_data, 'snippet_suffix', '.php');

print PhpSnippets\Base::get_shortcode($_POST['snippet_path'],$ext); 

/*EOF*/