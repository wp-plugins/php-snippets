<div class="wrap">
	<?php /*---------------- HEADER and TABS --------------------------- */ ?>
	<div id="php_snippets_header">
		<img src="<?php print PHP_SNIPPETS_URL; ?>/images/php-snippets-icon.png" alt="php-snippets logo" width="160" height="80" style="float:left; margin-right:20px;"/>
		<p class="php_snippets_header_text">PHP Snippets</span>
			<a href="<?php print $data['help']; ?>" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print PHP_SNIPPETS_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a>
		<br/>
		<span class="php_snippets_page_title"><?php print $data['page_title']; ?></span>
		</p>
	</div>

	<div id="php_snippets_mainmenu">
	</div>

	<?php 
	/* Any Message (e.g. notices and errors) */
	print $data['msg']; 
	?>

	<div id="php_snippets_nav"><?php print $data['menu']; ?></div>

	<?php 
	/* ----------------- MAIN PAGE CONTENT -------------------------------*/
	print $data['content']; 
	/* -------------------------------------------------------------------*/
	?>

	<?php /*--------------- FOOTER --------------------------*/ ?>
	<div id="php_snippets_footer">
		<p style="margin:10px;">
			<!--span class="php_snippets_link">
				<a href="#" target="_blank"><img class="php_snippets_img" src="<?php print PHP_SNIPPETS_URL; ?>/images/heart.png" height="32" width="32" alt="heart"/></a>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE" target="_blank"><?php _e('Get Support', 'php_snippets'); ?></a>
			</span-->
			<span class="php_snippets_link">
				<a href="http://code.google.com/p/wordpress-php-snippets/"><img class="php_snippets_img" src="<?php print PHP_SNIPPETS_URL; ?>/images/help.png" height="32" width="32" alt="help"/></a>
				<a href="http://craftsmancoding.com/products/downloads/support/"><?php _e('Get Professional Help!', 'php_snippets'); ?></a>
			</span>
			<span class="php_snippets_link">
				<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><img class="php_snippets_img" src="<?php print PHP_SNIPPETS_URL; ?>/images/bug.png" height="32" width="32" alt="bug"/></a>
				<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><?php _e('Report a Bug', 'php_snippets'); ?></a></span>
			<span class="php_snippets_link">
				<a href="http://wordpress.org/tags/custom-content-type-manager?forum_id=10" target="_blank"><img class="php_snippets_img" src="<?php print PHP_SNIPPETS_URL; ?>/images/forum.png" height="32" width="32" alt="forum"/></a>
				<a href="http://wordpress.org/tags/php-snippets?forum_id=10" target="_blank"><?php _e('Forum', 'php_snippets'); ?></a>
			</span>
		</p>
	</div>
</div>