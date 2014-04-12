<?php
/**
 * @description Gets the current date. Optionally supply a format
 * @param string $format (default:Y-m-d H:i:s)
 */
if (!isset($format) || empty($format)) {
	$format = get_option('date_format', 'Y-m-d H:i:s');
}

print date($format); 
/*EOF*/