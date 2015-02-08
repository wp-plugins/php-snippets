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
$data['content'] = '';
$parent_dir = isset($_POST['parent_dir']) ? $_POST['parent_dir'] : substr(get_home_path(), 0, -1);;
$dirs = PhpSnippets\Base::list_dirs($parent_dir);

// Each Directory gets a heading
foreach ($dirs as $i => $dir) {	
    $dir_name = explode('/', $dir);
	$data['content'] .= sprintf("<li>
		<a href='#' class='refresh_dir' data-parent_dir='$dir'><img class='php_snippets_img' src='".PHP_SNIPPETS_URL."/images/dir.png' height='10' width='10'/><strong>%s</strong></a>"
		, array_pop($dir_name)
	);
	if($i !== 0) {
		$data['content'] .= " : <a href='#' class='select_dir' data-sel_dir='$dir'><span class='linklike'>Select</span></li></a>";
	}
}

print PhpSnippets\Base::load_view('directory_list.php', $data);