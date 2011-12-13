<form method="post">
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	<label for="snippet_dir" class="php_snippets_label">Snippet Directory</label>
	<input type="text" name="snippet_dir" id="snippet_dir" size="100" value="<?php print htmlentities($data['value']); ?>"/>
	<div class="php_snippets_description">This is the directory where you can store your PHP snippets.  Any files you save there with the <code>.snippet.php</code> extension can be selected via the PHP Snippets TinyMCE button and used as a shortcode.  For maximum security, it is recommended that you place this <em>above</em> the root of your site so your PHP snippets are not accessible via a browser.<br/>
	Use a full path, e.g. <code>/home/user/dir/snippets</code> (omit the trailing slash).<br/>
	Make sure to update this value if you migrate your site to a new server.</div>
	<br/>
	<input type="submit" value="Update" class="button"/>
</form>