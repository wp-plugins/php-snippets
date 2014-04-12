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

foreach ($dirs as $dir => $exist) {
	$class_dir = $exist ? '' : 'snippet_dir_error';
	$class_dir_error = $exist ? '' : '<span>:This Directory Doesnt Exist.</span>';
	$data['content'] .= "<div class='snippet_dir $class_dir'>$dir $class_dir_error</div>";

	if($exist) {
		$snippets = PhpSnippets\Base::get_snippets($dir,$ext); 
		if(!empty($snippets)) {
			foreach ($snippets as $shortname => $snippet) {
				$info = PhpSnippets\Base::get_snippet_info($snippet);
				
				if (empty($info['shortcode'])) {
					$info['shortcode'] = "[$shortname]";
				}
				$data['content'] .= sprintf('<li>
					<strong class="linklike">%s</strong> 
					: <span class="php_snippets_desc">%s</span></li>'
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

print PhpSnippets\Base::load_view('tb_setting.php', $data);


/*EOF*/