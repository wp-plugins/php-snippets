<?php
/*
Description: Generates a link to a post or page based on its ID.
Shortcode: [link id=123]Click here[/link]
*/

if (!isset($id)) {
	print 'Missing id.';
	return;
}
if (!isset($content) || empty($content)) {
	$content = 'Click here';
}

printf('<a href="%s">%s</a>', get_permalink($id), $content);
/*EOF*/