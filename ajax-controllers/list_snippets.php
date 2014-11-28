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

//$snippets = PhpSnippets\Base::get_snippets(); 

$ps_data = get_option(PhpSnippets\Base::db_key, array());
$snippet_dirs = PhpSnippets\Base::get_value($ps_data, 'snippet_dirs', array());
$show_builtin_snippets = PhpSnippets\Base::get_value($ps_data, 'show_builtin_snippets', 0);
$ext = PhpSnippets\Base::get_value($ps_data, 'snippet_suffix');
$dirs = PhpSnippets\Base::get_dirs($snippet_dirs,$show_builtin_snippets);


if (!empty(PhpSnippets\Base::$warnings)) {
	$data['content'] = '<div id="php-snippets-errors" class="error">';
	foreach (PhpSnippets\Base::$warnings as $w => $tmp) {
		$data['content'] .= sprintf("<p>%s: %s</p>", $w, __('Directory Does not Exist','php_snippets'));
	}
	$data['content'] .= '</div><br>';	
}

foreach ($dirs as $dir => $exists) {

	if($exists) {
		$snippets = PhpSnippets\Base::get_snippets($dir,$ext);
		if(!empty($snippets)) {
			foreach ($snippets as $shortname => $snippet) {
				$info = PhpSnippets\Base::get_snippet_info($snippet);
				
				$shortcode = PhpSnippets\Base::get_shortcode($info,$shortname);
				$data['content'] .= sprintf("<li>
    				<strong class='linklike' onclick='javascript:insert_shortcode(\"%s\");'>%s</strong>
					: <span class='php_snippets_desc'>%s</span></li>"
					, htmlspecialchars(addslashes($shortcode))
					, $shortname
					, $info['desc']
				);
			}
		}
	}
}

print PhpSnippets\Base::load_view('thickbox.php', $data);


/*EOF*/