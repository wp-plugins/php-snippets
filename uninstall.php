<?php
/*------------------------------------------------------------------------------
This is run only when this plugin is uninstalled. All cleanup code goes here.

WARNING: uninstalling a plugin fails when developing locally via MAMP et al.
Perhaps related to how WP attempts (and fails) to connect to the local site?
------------------------------------------------------------------------------*/
if (defined('WP_UNINSTALL_PLUGIN')) {
	delete_option('php_snippets');
	delete_option('php_snippets_license_status');
	delete_option('php_snippets_license_key');	
}
/*EOF*/