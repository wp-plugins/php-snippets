<?php
/*
Description: Show wrapped content only 1st-time visitors.  The content will be hidden on subsequent page views.
Shortcode: [first_timers]Welcome newcomer's to this page![/first_time_visitors_only]

This script simply sets a cookie to determine whether or not the user has been to a page or not.
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