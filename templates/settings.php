<form method="post">
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	<label for="snippet_dir" class="php_snippets_label">Snippet Directory</label>
	<input type="text" name="snippet_dir" id="snippet_dir" size="100" value="<?php print htmlentities($data['value']); ?>"/>
	<div class="php_snippets_description">
		<p>This is the absolute path to directory where you can store your PHP snippets.  Use PHP's <code>getcwd()</code> or the Linux <code>pwd</code> command to get the full path to the directory.  <strong>DO NOT USE A URL!</strong> This MUST be a full path!</p>
		
		<p>Any files you save there with the <code>.snippet.php</code> extension can be selected via the PHP Snippets TinyMCE button and used as a shortcode.  For maximum security, it is recommended that you place this <em>above</em> the root of your site so your PHP snippets are not accessible via a browser.<br/>
	Use a full path, e.g. <code>/home/user/dir/snippets</code> (omit the trailing slash).<br/>
	Make sure to update this value if you migrate your site to a new server.</p></div>
	<br/>
	<input type="submit" value="Update" class="button"/>
</form>