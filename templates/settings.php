<?php  
if(!empty($data['warnings'])) : ?>
	<div id="php-snippets-errors" class="error"><p>Some of the directories you defined do not exist!</p></div>
<?php endif; ?>
<form method="post">
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<?php print $data['licensing_fields']; ?>
	<?php //print htmlentities($data['snippet_dir']); ?>
	<label for="snippet_dir" class="php_snippets_label">Snippet Directory</label>
	<div class="php_snippets_description">
	   <p>Choose one or more directories that contain your PHP Snippets. These should be full paths, not URLs, e.g. <code>/home/html/wp-content/snippets</code> (omit the trailing slash).  Use PHP's <code>getcwd()</code> or the Linux <code>pwd</code> command to get the full path to the directory.  You may use the <code>[+ABSPATH+]</code> placeholder to get a calculated path to your site root, e.g. <code>[+ABSPATH+]mysnippets</code>. If you use absolute paths instead of the placeholder be sure to update this setting if you move your site!</p>
	</div>
	<div id="dir_wrap">
		<?php if(!empty($data['snippet_dirs'])) : ?>
			<?php foreach ($data['snippet_dirs'] as $dir) : ?>
				<div class="dir_item <?php print isset($data['warnings'][$dir]) ? 'warning_field' : ''; ?>">
					<input type="text" name="snippet_dirs[]" class="snippet_dir" size="100" value="<?php print $dir; ?>"/><span class='rm_dir'>x</span>
					<?php print isset($data['warnings'][$dir]) ? '<span class="warn_info" >This Directory does not Exist.</span>' : ''; ?>
				</div>
				
			<?php endforeach; ?>
		<?php else : ?>
			<div class="dir_item"><input type="text" name="snippet_dirs[]" class="snippet_dir" size="100" value=""/><span class='rm_dir'>x</span></div>
		<?php endif ?>
	</div>
	
	<!-- <button onclick="javascript:add_field_dir(event);"class="button" id="add_dir">Add Directory</button> -->
	<a href="#" onclick="modal_directory(event);" id="add_dir" class="button">Add Directory</a>
	<br/>
	<br/>		
       <label for="snippet_suffix" class="php_snippets_label">Snippet Suffix</label>
	   <input type="text" name="snippet_suffix" id="snippet_suffix" size="50" value="<?php print !empty($data['snippet_suffix']) ? htmlentities($data['snippet_suffix']) : '.snippet.php'; ?>"/>

    	<div class="php_snippets_description">
    		<p>Enter the file extension for files that will be listed as available snippets. The default is <code>.snippet.php</code></p>
    	</div>

	<input type="hidden" name="show_builtin_snippets" value="0" />
	<input type="checkbox" id="show_builtin_snippets" name="show_builtin_snippets" value="1" <?php print ($data['show_builtin_snippets'] == 1) ? 'checked' : ''; ?>/>
	<label for="show_builtin_snippets">Show Built-in Snippets</label><br><br>

	<input type="hidden" name="show_tmce_button" value="0" />
	<input type="checkbox" id="show_tmce_button" name="show_tmce_button" value="1" <?php print ($data['show_tmce_button'] == 1) ? 'checked' : ''; ?>/>
	<label for="show_tmce_button">Show TinyMCE Button</label><br><br>


	<a href="#" onclick="settings_snippets(event);" id="show_all_snippets" class="button">Show all Snippets</a>
	
	<br><br>
	<input type="submit" value="Update Settings" class="button button-primary"/>
</form>
