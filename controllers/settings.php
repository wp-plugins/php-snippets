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

$ps_data = get_option(Phpsnippets\Base::db_key, array());
$data['licensing_fields'] = ob_get_clean();
$data['snippet_dirs'] = Phpsnippets\Base::get_value($ps_data, 'snippet_dirs', array());
$data['warnings'] = Phpsnippets\Base::get_value($ps_data, 'warnings', array());
$data['snippet_suffix'] = Phpsnippets\Base::get_value($ps_data, 'snippet_suffix');
$data['show_builtin_snippets'] = Phpsnippets\Base::get_value($ps_data, 'show_builtin_snippets');

// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
  
    if (isset($_POST['activate_license'])) {
        if (PhpSnippets\License::activate($_POST['license_key'])) {
            $data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>', 'Your license has been successfully activated. Thank you for your support!');
            ob_start();
            PhpSnippets\License::get_fields();
            $data['licensing_fields'] = ob_get_clean();            
        }
        else {
            $data['msg'] .= sprintf('<div class="error"><p>%s</p></div>', 'There was a problem activating your license. Sorry for the inconvenience.');
            ob_start();
            PhpSnippets\License::get_fields();
            $data['licensing_fields'] = ob_get_clean(); 
        }
        // options-general.php?page=php-snippets
        //print '<script type="text/javascript">window.location.replace("'.get_admin_url(false, 'options-general.php?page=php-snippets').'");</script>';
    }  
    else {   

    	// A little cleanup before we handoff to save_definition_filter

    	$snippet_dirs = Phpsnippets\Base::get_value($_POST, 'snippet_dirs',array());
    	$snippet_suffix = Phpsnippets\Base::get_value($_POST, 'snippet_suffix');
        $show_builtin_snippets = Phpsnippets\Base::get_value($_POST, 'show_builtin_snippets');
    	$snippet_suffix = !empty($snippet_suffix) ? trim(strip_tags($snippet_suffix)) : '.snippet.php';
        foreach ($snippet_dirs as $i => $dir) {
            // remove empty val
            if(trim($dir) == '') {
                unset($snippet_dirs[$i]);
            }

            if (!PhpSnippets\Base::dir_exists($dir)){

                $warns = PhpSnippets\Base::$warnings;

            }
        }

        $data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>', 'Your settings have been updated!');
         
        $ps_data['snippet_dirs'] = $snippet_dirs;
        $ps_data['snippet_suffix'] = $snippet_suffix;
        $ps_data['show_builtin_snippets'] = $show_builtin_snippets;
        
        update_option(Phpsnippets\Base::db_key, $ps_data);
        $data['snippet_dirs'] = $snippet_dirs;
        $data['warnings'] = $warns;
        $data['snippet_suffix'] = $snippet_suffix;
        $data['show_builtin_snippets'] = $show_builtin_snippets;
    }
}

$data['content'] .= Phpsnippets\Base::load_view('settings.php', $data);

print Phpsnippets\Base::load_view('default.php', $data);

/*EOF*/