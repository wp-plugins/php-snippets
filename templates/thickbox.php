<style>
	.linklike {
		cursor:pointer;
		color: #21759B;
	}
	
	span.php-snippets-link {
		padding: 10px;
		margin-left: 5px;
	}
	img.php-snippets-img {
		vertical-align: middle;
		margin-right: 10px;
	}
</style>

<h2><?php print $data['pagetitle']; ?> 
			<a href="http://code.google.com/p/wordpress-php-snippets/wiki/SnippetSelector" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print PHP_SNIPPETS_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a></h2>

<p>Below are listed PHP Snippets that you have installed on your site.  Select one to add its shortcode to your post or page.  If you would like to add your own PHP Snippets to this list, simply create a PHP file in your snippet directory and save it using a <code>.snippet.php</code> extension. (See <strong>Settings &rarr; PHP Snippets</strong> to configure your Snippet directory).</p>

<ul>
<?php print $data['content']; ?>
</ul>

<div id="php_snippets_footer">
	<p style="margin:10px;">
		<span class="php-snippets-link">
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE" target="_blank"><img class="php-snippets-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/heart.png" height="32" width="32" alt="heart"/></a>
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WPR4A9JT355BE" target="_blank"><?php _e('Support this Plugin', 'php_snippets'); ?></a>
		</span>
		<span class="php-snippets-link">
			<a href="http://code.google.com/p/wordpress-php-snippets/"><img class="php-snippets-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/help.png" height="32" width="32" alt="help"/></a>
			<a href="http://code.google.com/p/wordpress-php-snippets/"><?php _e('Documentation', 'php_snippets'); ?></a>
		</span>
		<span class="php-snippets-link">
			<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><img class="php-snippets-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/bug.png" height="32" width="32" alt="bug"/></a>
			<a href="http://code.google.com/p/wordpress-php-snippets/issues/list"><?php _e('Report a Bug', 'php_snippets'); ?></a></span>
		<span class="php-snippets-link">
			<a href="http://wordpress.org/tags/php-snippets?forum_id=10" target="_blank"><img class="php-snippets-img" src="<?php print PHP_SNIPPETS_URL; ?>/images/forum.png" height="32" width="32" alt="forum"/></a>
			<a href="http://wordpress.org/tags/php-snippets?forum_id=10" target="_blank"><?php _e('Forum', 'php_snippets'); ?></a>
		</span>
	</p>
</div>