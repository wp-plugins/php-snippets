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


$snippets = PhpSnippets\Functions::get_snippets(); 

if (!empty(PhpSnippets\Functions::$warnings)) {
	$data['content'] = '<div id="php-snippets-errors" class="error"><p><ul>';
	foreach (PhpSnippets\Functions::$warnings as $w => $tmp) {
		$data['content'] .= sprintf('<li>%s</li>', $w);
	}
	$data['content'] .= '<ul></p></div>';	
}
$dir ='';
foreach($snippets as $shortname => $s) {
	
	if (empty($d['shortcode'])) {
		$s['shortcode'] = "[$shortname]";
	}
	if($s['dir'] == $dir) {
		$data['content'] .= sprintf('<li>
								<strong class="linklike">%s</strong> 
								: <span class="php_snippets_desc">%s</span></li>'
								, $shortname
								, $s['desc']
							);
	} else {
		$dir = $s['dir'];
		$data['content'] .= "<li class='snippet_dir'><strong>Directory: </strong>{$dir}</li>";
		$data['content'] .= sprintf('<li>
								<strong class="linklike">%s</strong> 
								: <span class="php_snippets_desc">%s</span></li>'
								, $shortname
								, $s['desc']
							);
	}
	

}
$php_license = PhpSnippets\License::check();
if($php_license !==  'valid') {
	$data['content'] = PhpSnippets\License::get_error_msg();
}

print PhpSnippets\Functions::load_view('tb_setting.php', $data);


/*EOF*/