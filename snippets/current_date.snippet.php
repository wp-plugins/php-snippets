<?php
/*
Description: Gets the current date. Optionally supply a format
Shortcode: [current_date]
*/
if (!isset($format) || empty($format)) {
	$format = get_option('date_format', 'Y-m-d H:i:s');
}

print date($format); 
/*EOF*/