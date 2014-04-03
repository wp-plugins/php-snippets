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

$snippets = PhpSnippets\Functions::get_snippets(); 

if (!empty(PhpSnippets\Functions::$warnings)) {
	$data['content'] = '<div id="php-snippets-errors" class="error"><p><ul>';
	foreach (PhpSnippets\Functions::$warnings as $w => $tmp) {
		$data['content'] .= sprintf('<li>%s</li>', $w);
	}
	$data['content'] .= '<ul></p></div>';	
}

foreach($snippets as $shortname => $d) {
	if (empty($d['shortcode'])) {
		$d['shortcode'] = "[$shortname]";
	}
	$data['content'] .= sprintf('<li>
		<strong class="linklike" onclick="javascript:insert_shortcode(\'%s\');">%s</strong> 
		: <span class="php_snippets_desc">%s</span></li>'
		, htmlspecialchars(addslashes($d['shortcode']))
		, $shortname
		, $d['desc']
	);

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