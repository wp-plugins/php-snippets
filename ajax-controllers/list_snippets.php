<?php
/*------------------------------------------------------------------------------
Fires when a user clicks the button in the TinyMCE editor:
this launches the thickbox pop-up that displays the list of all available snippets.
------------------------------------------------------------------------------*/
if (!defined('PHP_SNIPPETS_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) die('You do not have permission to do that.');

// Template variables
$data = array();
$data['pagetitle'] = __('Select a Snippet', 'php_snippets');
$data['content'] = '';
$data['other_shortcodes'] = '';

//$snippets = PhpSnippets\Functions::get_snippets(); 

$ps_data = get_option(PhpSnippets\Functions::db_key, array());
$snippet_dirs = PhpSnippets\Functions::get_value($ps_data, 'snippet_dirs', array());
$show_builtin_snippets = PhpSnippets\Functions::get_value($ps_data, 'show_builtin_snippets', 0);
$ext = PhpSnippets\Functions::get_value($ps_data, 'snippet_suffix');
$dirs = PhpSnippets\Functions::get_dirs($snippet_dirs,$show_builtin_snippets);


if (!empty(PhpSnippets\Functions::$warnings)) {
	$data['content'] = '<div id="php-snippets-errors" class="error">';
	foreach (PhpSnippets\Functions::$warnings as $w => $tmp) {
		$data['content'] .= sprintf('<p>%s : This Directory Doesnt Exist</p>', $w);
	}
	$data['content'] .= '</div><br>';	
}

foreach ($dirs as $dir => $exist) {

	if($exist) {
		$snippets = PhpSnippets\Functions::get_snippets($dir,$ext); 
		if(!empty($snippets)) {
			foreach ($snippets as $shortname => $snippet) {
				$info = PhpSnippets\Functions::get_snippet_info($snippet);
				
				if (empty($info['shortcode'])) {
					$info['shortcode'] = "[$shortname]";
				}
				$data['content'] .= sprintf('<li>
					<strong class="linklike" onclick="javascript:insert_shortcode(\'%s\');">%s</strong> 
					: <span class="php_snippets_desc">%s</span></li>'
					, htmlspecialchars(addslashes($info['shortcode']))
					, $shortname
					, $info['desc']
				);
			}
		}
	}
}


$php_license = PhpSnippets\License::check();
if($php_license !==  'valid') {
	$data['content'] = PhpSnippets\License::get_error_msg();
}
/*
foreach (PhpSnippets\Functions::$existing_shortcodes as $shortname) {
	$data['other_shortcodes'] = '<strong class="linklike" onclick="javascript:insert_shortcode(\'['.$shortname.']\');">'.$shortname.'</strong><br/>';
}
*/
print PhpSnippets\Functions::load_view('thickbox.php', $data);


/*EOF*/