<?php
/**
 * This script simply sets a cookie to determine whether or not the user has been to a page or not, then
 * displays wrapped content to viewers.
 *
 * @description Show wrapped content only to 1st-time visitors.
 * @ s h o rtcode [first_timers]Welcome newcomer's to this page![/first_timers]
 * @content Welcome newcomer's to this page!
 */
global $post;

$this_page = 0;
if (isset($post)) {
	$this_page = $post->ID;
}


$this_page_cookie_key = 'i_visited_page_'.$this_page;

if ( !isset($_COOKIE[ $this_page_cookie_key ]) ) {
	// Set the new cookie, expires 10 years from now
	setcookie($this_page_cookie_key, 1, time()+3600*24*365*10);
	print $content;	
}


/*EOF*/