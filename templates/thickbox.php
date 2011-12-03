<style>
	.linklike {
		cursor:pointer;
		color: #21759B;
	}
</style>

<h2><?php print $data['pagetitle']; ?></h2>

<p>Below are listed PHP Snippets that you have installed on your site.  Select one to add its shortcode to your post or page.  If you would like to add your own PHP Snippets to this list, simply create a PHP file in the <strong>wp-content/plugins/php-snippets/snippets</strong> directory and save it using a <code>.snippet.php</code> extension.</p>

<?php print $data['content']; ?>

<div id="php_snippets_footer">
	<p style="margin:10px;">
		<span class="php-snippets-link">
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE" target="_blank"><img class="cctm-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/heart.png" height="32" width="32" alt="heart"/></a>
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE" target="_blank"><?php _e('Support this Plugin', 'php_snippets'); ?></a>
		</span>
		<span class="php-snippets-link">
			<a href="http://code.google.com/p/wordpress-php-snippets/"><img class="cctm-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/help.png" height="32" width="32" alt="help"/></a>
			<a href="http://code.google.com/p/wordpress-php-snippets/"><?php _e('Documentation', 'php_snippets'); ?></a>
		</span>
		<span class="php-snippets-link">
			<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><img class="cctm-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/bug.png" height="32" width="32" alt="bug"/></a>
			<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><?php _e('Report a Bug', 'php_snippets'); ?></a></span>
		<span class="php-snippets-link">
			<a href="http://wordpress.org/tags/php-snippets?forum_id=10" target="_blank"><img class="cctm-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/forum.png" height="32" width="32" alt="forum"/></a>
			<a href="http://wordpress.org/tags/php-snippets?forum_id=10" target="_blank"><?php _e('Forum', 'php_snippets'); ?></a>
		</span>
	</p>
	<p><em><small>The <a href="http://code.google.com/p/wordpress-php-snippets/">PHP Snippets</a> plugin was developed by Everett Griffiths.</small></em></p>
</div>