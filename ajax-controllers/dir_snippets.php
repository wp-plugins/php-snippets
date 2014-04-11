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
$dirs = PhpSnippets\Functions::get_snippet_dirs(); 
$snippets = array();

if (!empty(PhpSnippets\Functions::$warnings)) {
	$data['content'] .= '<div id="php-snippets-errors" class="error"><p>Some of the directories you defined do not exist!</p></div><br>';	
}


foreach ($dirs as $dir) {
	$data['content'] .= "<div class='snippet_dir'>$dir</div>";
	$snippets = PhpSnippets\Functions::get_snippets2($dir); 

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
	}
	

}

$php_license = PhpSnippets\License::check();
if($php_license !==  'valid') {
	$data['content'] = PhpSnippets\License::get_error_msg();
}

print PhpSnippets\Functions::load_view('tb_setting.php', $data);


/*EOF*/