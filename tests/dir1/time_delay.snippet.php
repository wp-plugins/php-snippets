<?php
/*
Description: Make wrapped content appear after X seconds.
Shortcode: [time_delay secs="30"]I am invisible until so many seconds have elapsed.[/time_delay]

This script will hide the wrapped text for the number of seconds defined by the sec parameter.
*/

// Bail if this isn't set up correctly.
if (!isset($secs) || empty($secs) || $secs == 0 || !is_numeric($secs)) {
	print $content;
	return;
}

// Proceed: convert to milliseconds
$milliseconds = (int) $secs * 1000;

?>
<script type="text/javascript">
	setTimeout('show_time_delayed_content();', <?php print $milliseconds; ?>);

	function show_time_delayed_content(){
	    document.getElementById('time_delayed_content').style.display='block';
	}
</script>
<div id="time_delayed_content" style="display:none;"><?php print $content; ?></div>
