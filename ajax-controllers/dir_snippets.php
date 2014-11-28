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


$ps_data = get_option(PhpSnippets\Base::db_key, array());
$snippet_dirs = PhpSnippets\Base::get_value($ps_data, 'snippet_dirs', array());
$show_builtin_snippets = PhpSnippets\Base::get_value($ps_data, 'show_builtin_snippets', 0);
$ext = PhpSnippets\Base::get_value($ps_data, 'snippet_suffix');
$dirs = PhpSnippets\Base::get_dirs($snippet_dirs,$show_builtin_snippets);

if (!empty(PhpSnippets\Base::$warnings)) {
	$data['content'] .= '<div id="php-snippets-errors" class="error"><p>Some of the directories you defined do not exist!</p></div><br>';	
}

// Each Directory gets a heading
foreach ($dirs as $dir => $exists) {
	$class_dir = $exists ? '' : 'snippet_dir_error';
	$class_dir_error = $exists ? '' : '<span>: '.__('Directory Does not Exist','php_snippets').'</span>';
	$data['content'] .= "<div class='snippet_dir $class_dir'>$dir $class_dir_error</div>";

	if(!$exists) continue;
	
	$snippets = PhpSnippets\Base::get_snippets($dir,$ext);

	if(!empty($snippets)) {
		foreach ($snippets as $shortname => $snippet) {
			$info = PhpSnippets\Base::get_snippet_info($snippet);
			//print '<pre>'; print_r($info); print '</pre>';
            $info['shortcode'] = PhpSnippets\Base::get_shortcode($info,$shortname);

			$error_class = '';
			if ($info['errors']) {
                $info['desc'] = $info['errors'];
                $error_class = ' php_snippets_error warning_field';
			}
			
			$data['content'] .= sprintf('<li>
				<strong class="linklike %s">%s</strong> 
				: <span class="php_snippets_desc">%s</span></li>'
				, $error_class
				, $shortname
				, $info['desc']
			);
		}
	}

}

print PhpSnippets\Base::load_view('tb_setting.php', $data);


/*EOF*/