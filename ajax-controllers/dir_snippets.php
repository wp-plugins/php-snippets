<?php
/*------------------------------------------------------------------------------
Fires when a user clicks the button in the TinyMCE editor:
this launches the thickbox pop-up that displays the list of all available snippets.
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

// Template variables
$data = array();
$data['pagetitle'] = __('Snippets and Directory List', 'php_snippets');
$data['content'] = '';


$ps_data = get_option(PhpSnippets\Functions::db_key, array());
$snippet_dirs = PhpSnippets\Functions::get_value($ps_data, 'snippet_dirs', array());
$show_builtin_snippets = PhpSnippets\Functions::get_value($ps_data, 'show_builtin_snippets', 0);
$ext = PhpSnippets\Functions::get_value($ps_data, 'snippet_suffix');
$dirs = PhpSnippets\Functions::get_dirs($snippet_dirs,$show_builtin_snippets);

if (!empty(PhpSnippets\Functions::$warnings)) {
	$data['content'] .= '<div id="php-snippets-errors" class="error"><p>Some of the directories you defined do not exist!</p></div><br>';	
}

foreach ($dirs as $dir => $exist) {
	if($exist) {
		$snippets = PhpSnippets\Functions::get_snippets($dir,$ext); 
	}
}
echo '<pre>';
print_r($snippets);
die();
foreach ($snippets as $snippet) {
	echo '<pre>';
	print_r($snippet);
	die();
	$info = PhpSnippets\Functions::get_snippet_info($snippet);
	echo '<pre>';
	print_r($info);
	die();
}
/*
	$data['content'] .= "<div class='snippet_dir'>$dir</div>";
	if(!empty($snippets)) {
		foreach ($snippets as $shortname => $s) {
			if (empty($d['shortcode'])) {
				$s['shortcode'] = "[$shortname]";
			}
			$data['content'] .= sprintf('<li>
				<strong class="linklike">%s</strong> 
				: <span class="php_snippets_desc">%s</span></li>'
				, $shortname
				, $s['desc']
			);
		}
	}*/

$php_license = PhpSnippets\License::check();
if($php_license !==  'valid') {
	$data['content'] = PhpSnippets\License::get_error_msg();
}

print PhpSnippets\Functions::load_view('tb_setting.php', $data);


/*EOF*/