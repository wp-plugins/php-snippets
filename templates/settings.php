<form method="post">
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<?php print $data['licensing_fields']; ?>
	
	<label for="snippet_dir" class="php_snippets_label">Snippet Directory</label>
	<input type="text" name="snippet_dir" id="snippet_dir" size="100" value="<?php print htmlentities($data['snippet_dir']); ?>"/>
	

	<div class="php_snippets_description">
		<p>This is the absolute path to the directory where you can store your PHP snippets, e.g. <code>/home/html/wp-content/snippets</code> (omit the trailing slash).  Use PHP's <code>getcwd()</code> or the Linux <code>pwd</code> command to get the full path to the directory.  <strong>DO NOT USE A URL!</strong> This MUST be a full path!  You may use the <code>[+ABSPATH+]</code> placeholder to get a calculated path to your site root, otherwise be sure to update this setting if you move your site!</p>
    </div>
    		
	
		<label for="snippet_suffix" class="php_snippets_label">Snippet Suffix</label>
	   <input type="text" name="snippet_suffix" id="snippet_suffix" size="50" value="<?php print !empty($data['snippet_suffix']) ? htmlentities($data['snippet_suffix']) : '.snippet.php'; ?>"/>

    	<div class="php_snippets_description">
    		<p>Enter the file extension for files that will be listed as available snippets. The default is <code>.snippet.php</code></p>
    	</div>

	<input type="hidden" name="show_builtin_snippets" value="0" />
	<input type="checkbox" id="show_builtin_snippets" name="show_builtin_snippets" value="1" <?php print ($data['show_builtin_snippets'] == 1) ? 'checked' : ''; ?>/>
	<label for="show_builtin_snippets">Show Built-in Snippets</label>
	
	<br><br>
	<input type="submit" value="Update Settings" class="button button-primary"/>
</form>