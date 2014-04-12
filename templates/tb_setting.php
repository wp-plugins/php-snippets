<style>
	.linklike {
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
	.snippet_list li {
		margin-left: 20px;
	}
	.snippet_dir {
		margin-left: 0px;
		font-size: 13px;
		background: #ddd;
		padding: 5px 3px;
		font-weight: bold;
		margin-bottom:5px; 
	}
	.snippet_dir span {
		font-size: 11px;
		margin-left: 5px;
	}
	.snippet_dir_error {
		border: 1px solid #ae0004;
		background: #fdbdbd;
		color: #ae0004;
	}
</style>
<div>
	<h2><?php print $data['pagetitle']; ?> 
		<a href="http://code.google.com/p/wordpress-php-snippets/wiki/SnippetSelector" target="_new" title="Contextual Help" style="text-decoration: none;">
			<img src="<?php print PHP_SNIPPETS_URL; ?>/images/question-mark.gif" width="16" height="16" />
		</a></h2>

	<p>Below are listed PHP Snippets that you have installed on your site.</p>

	<ul class="snippet_list">
	<?php print $data['content']; ?>
	</ul>

</div>
