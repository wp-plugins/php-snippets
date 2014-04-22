<?php
/*------------------------------------------------------------------------------
Fires when a user clicks the button in the TinyMCE editor:
this launches the thickbox pop-up that displays the list of all available snippets.
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

// Template variables

$data = array();
$data['pagetitle'] = __('Directory List', 'php_snippets');

// hardcoded for now, im still tryign to figure out how to pass an argument on ajax controller
$dirs = PhpSnippets\Base::list_dirs('D:/dev/wp.rddw.com');

// Each Directory gets a heading
foreach ($dirs as $dir) {		
	$data['content'] .= sprintf('<li>
		<a href="#"><strong>%s</strong></a>
		: <span class="linklike">Select</span></li>'
		, $dir
	);
}

print PhpSnippets\Base::load_view('directory_list.php', $data);