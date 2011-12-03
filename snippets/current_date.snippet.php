<?php
/**
 * DESCRIPTION: Gets the current date.
 */
$format = get_option('date_format', 'Y-m-d H:i:s');
print date($format); 
/*EOF*/