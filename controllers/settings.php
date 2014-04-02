<?php
/*------------------------------------------------------------------------------
Handles the Settings page for this plugin where users can edit various options
------------------------------------------------------------------------------*/
if (!current_user_can('administrator')) exit('Admins only.');

// Page variables
$data = array();
$data['page_title'] = 'Settings';
$data['content']	= '';
$data['help'] = 'http://code.google.com/p/wordpress-php-snippets/';
$data['msg'] = '';
$data['menu'] = '';
$data['submit'] = __('Save', 'php_snippets');
$data['action_name']  = 'php_snippets_settings';
$data['nonce_name']  = 'php_snippets_settings_nonce';

ob_start();
PhpSnippets\License::get_fields();
$data['licensing_fields'] = ob_get_clean();
$data['snippet_dir'] = self::get_value(self::$data, 'snippet_dir', ABSPATH.'/wp-content/snippets/');
$data['snippet_suffix'] = self::get_value(self::$data, 'snippet_suffix');

//$php_license = PhpSnippets\License::check();
//PhpSnippets\License::activate_page();

// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
  
    if (isset($_POST['activate_license'])) {
        if (PhpSnippets\License::activate(self::get_value($_POST, 'license_key'))) {
            $data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>', 'Your license has been successfully activated. Thank you for your support!');
            ob_start();
            PhpSnippets\License::get_fields();
            $data['licensing_fields'] = ob_get_clean();            
        }
        else {
            $data['msg'] .= sprintf('<div class="error"><p>%s</p></div>', 'There was a problem activating your license. Sorry for the inconvenience.');
        }
        // options-general.php?page=php-snippets
        //print '<script type="text/javascript">window.location.replace("'.get_admin_url(false, 'options-general.php?page=php-snippets').'");</script>';
    }  
    else {    
    	// A little cleanup before we handoff to save_definition_filter
    	$snippet_dir = trim(strip_tags(self::get_value($_POST, 'snippet_dir')));
    	$snippet_suffix = self::get_value($_POST, 'snippet_suffix');
    	$snippet_suffix = !empty($snippet_suffix) ? trim(strip_tags($snippet_suffix)) : '.snippet.php';
    
    
    	if (!PhpSnippets\Functions::check_permissions($snippet_dir)){
    		if (!empty(PhpSnippets\Functions::$warnings)) {
    			$data['content'] = '<div id="php-snippets-errors" class="error"><p><ul>';
    			foreach (PhpSnippets\Functions::$warnings as $w => $tmp) {
    				$data['content'] .= sprintf('<li>%s</li>', $w);
    			}
    			$data['content'] .= '<ul></p></div>';	
    		}
    	}
    	else {
    		$data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>', 'Your settings have been updated!');
    		self::$data['snippet_dir'] = $snippet_dir;
    		self::$data['snippet_suffix'] = $snippet_suffix;
    		
    		update_option(self::db_key, self::$data);
    		$data['value'] = $snippet_dir;
    		$data['snippet_suffix'] = $snippet_suffix;
    	
    	}
    }
}
$data['content'] .= self::load_view('settings.php', $data);

print self::load_view('default.php', $data);

/*EOF*/