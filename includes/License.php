<?php
/**
 * Check license  
 */
namespace PhpSnippets;

class License {

	public static $store_url = 'http://craftsmancoding.com/products/'; // store_url
	public static $product_url = 'http://craftsmancoding.com/products/downloads/php-snippets/'; 
	public static $plugin = 'PHP Snippets'; // item name from store
    public static $key_option_name = 'php_snippets_license_key';
    public static $status_option_name = 'php_snippets_license_status';
    
	public function __construct() 
	{	
		//$this->register_option();
		//$this->activate(); // should only run when data is posted
	}

	/**
	 * Get any form fields needed for the licensing
	 * Note that this form defines an alternate submit button:
	 *     activate_license
	 */
	public static function get_fields() {
		$license 	= get_option( self::$key_option_name );
		$status 	= get_option( self::$status_option_name );
		?>
		<div class="wrap php_snippets_licensing_info" style="border:1px dotted gray; padding:10px; margin:10px;">
			<h3><?php _e('Plugin PHP Snippets License Options'); ?><a href="https://code.google.com/p/wordpress-php-snippets/wiki/LicenseActivation" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print PHP_SNIPPETS_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a></h3>		

            <label for="snippet_dir" class="php_snippets_label"><?php _e('License Key'); ?></label>
        	<input type="text" name="license_key" id="license_key" size="30" value="<?php esc_attr_e($license); ?>"/>
            <br/><br/>
            <label for="license_status" class="php_snippets_label"><?php _e('License Status'); ?>:
                <?php if($status == 'valid') : ?>
                    <span style="color:green; font-weight:bold;"><?php _e('Active'); ?></span>
                <?php else: ?>
                    <span style="color:red; font-weight:bold;" class="php_snippets_invalid_license"><?php _e('Invalid'); ?></span>
                <?php endif; ?>            
            </label>
            
            <?php if($status != 'valid') : ?>
                <input type="submit" class="button-secondary" name="activate_license" value="<?php _e('Activate License'); ?>"/>
            <?php endif; ?>
            
            <p class="description">This plugin requires a license.  There is no fixed price. You can pay any amount that you feel is fair, including free. Don't be shy, <a href="http://craftsmancoding.com/products/downloads/php-snippets/">Get a License Key Here</a>!</p>
        </div>

		<?php
		
	}

    /**
     * Used in admin notices: show an error message to users re the license
     */
	public static function get_error_msg() {
	   return '<div id="php-warning" class="error">
                <p><strong>PHP Snippets is almost ready.</strong> You must <a href="options-general.php?page=php-snippets">Enter a License Key</a> for it to work.</p>
            </div>';
	}

	/**
	* register_option
	**/
	public static function register_option() {
		// creates our settings in the options table
		register_setting('php_license', self::$key_option_name, 'PhpSnippets\License::sanitize');
	}

	
	/**
	 * menu
	 * Add Plugin License Menu
	 * This is a prepared function to add Custom Menu for the plugin
	 * Usage: optional
	 * They can add a custom menu as a sub page for Activate License
	 */
/*
	public static function menu() {
		add_plugins_page( 'Activate '.self::$plugin.' License', 'Activate ' .self::$plugin. ' License', 'administrator', 'activate-' .strtolower(str_replace(' ', '_', self::$plugin)). '-license', array('PHP_License','activate_page') );
	}
*/

	/**
	 * Handles 
	 */
	public static function sanitize( $new ) {
		$old = get_option(self::$key_option_name);
		if( $old && $old != $new ) {
			delete_option(self::$status_option_name); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	 * activate the license
	 * @param string $license key
	 */
	public static function activate($license) {
        $license = trim($license);

        update_option(self::$key_option_name, $license);
        
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
			'url'       => home_url(),
			'rand' => uniqid().md5(home_url()) // cache-busting
		);
	
		// Call the custom API.
		$endpoint = add_query_arg( $api_params, self::$store_url );
		$response = wp_remote_get($endpoint);

		// make sure the response came back okay
		if (empty($response) || is_wp_error($response)) return false;
 
		// decode the license data
		$license_data = json_decode(wp_remote_retrieve_body($response));
		if (empty($license_data) || !is_object($license_data)) return false;
		// $license_data->license will be either "valid" or "invalid".  Should be named "status" :(
		update_option(self::$status_option_name, $license_data->license);
 
	}

	/**
	* Check that the license is valid.
	* cache the result using set_transient
	**/
	public static function check() {	
		$license      = trim( get_option(self::$key_option_name));
		$status       = get_option(self::$status_option_name);
		$cache_key    = strtolower(str_replace(' ', '_', self::$plugin));
		$data         = get_transient($cache_key);
		$key_old = trim(get_option(self::$key_option_name));
	
		if ($data && $key_old == $data->key) {
			return $status;
		} 
		else {
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'check_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
				'url'       => home_url()
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, self::$store_url ) );
	 
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			$data->key = trim( get_option( self::$key_option_name ) );

	 		set_transient( $cache_key, $data, 60*60 );
			return $status;	
		}
				
	}

}

//register setting
add_action('admin_init', function(){
	new License();
});