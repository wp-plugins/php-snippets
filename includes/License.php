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
    
    public static function get_status() {
        return get_option(self::$status_option_name);
    }
    
	/**
	 * Get any form fields needed for the licensing
	 * Note that this form defines an alternate submit button:
	 *     activate_license
	 */
	public static function get_fields() {
		$license 	= get_option( self::$key_option_name );
		$status 	= self::get_status();
		?>
		<div class="wrap php_snippets_licensing_info <?php print ($status == 'valid') ? 'php_snippets_valid' : 'php_snippets_invalid' ?>">
			<h3><?php _e('Plugin PHP Snippets License Options'); ?><a href="https://code.google.com/p/wordpress-php-snippets/wiki/LicenseActivation" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print PHP_SNIPPETS_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a></h3>		

            <label for="snippet_dir" class="php_snippets_label"><?php _e('License Key'); ?></label>
        	<input type="text" name="license_key" id="license_key" size="30" value="<?php esc_attr_e($license); ?>"/>

        	 <input type="submit" class="button-secondary" name="activate_license" value="<?php _e('Activate License'); ?>"/>

                <?php if($status == 'valid') : ?>
                    <label for="license_status" class="php_snippets_label license_status">
                        <?php _e('License Status'); ?>:
                        <span style="color:green; font-weight:bold;"><?php _e('Active'); ?></span>
                    </label>
                    
                    <p class="description">Your license is valid for this site.  Thank you for your business!</p>
                <?php else: ?>
                    <label for="license_status" class="php_snippets_label license_status">
                        <?php _e('License Status'); ?>:
                        <span style="color:red; font-weight:bold;" class="php_snippets_invalid_license"><?php _e('Invalid'); ?></span>
                    </label>
                   
                    <p class="description">This plugin requires a license.  There is no fixed price. You can pay any amount that you feel is fair, <em>including free</em>. Don't be shy, <a href="http://craftsmancoding.com/products/downloads/php-snippets/">Get a License Key</a>, then paste it here!</p>
                <?php endif; ?>            
            
            
        </div>

		<?php
		
	}

    /**
     * Used in admin notices: show an error message to users re the license
     */
	public static function get_error_msg() {
	   return '<div id="php-warning" class="error">
                <p><strong>PHP Snippets is almost ready.</strong> You must <a href="options-general.php?page=php-snippets">Enter a License Key</a> for it to work.  <a href="'.self::$product_url.'" target="_blank">Get a License Key here.</a></p>
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
	 * activate the license, return true on success
	 *
	 * @param string $license key
	 *
	 * @return bool
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

		update_option(self::$status_option_name, $license_data->license);

		if($license_data->success) {
			return true;
		}
		return false;
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
				'url'       => home_url(),
				'rand' => uniqid() // cache-busting
			);

			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, self::$store_url ) );

            /*
            // If the request is blocked (e.g. by firewall rules), then the response is something like 
            // the following:
            WP_Error Object
            (
                [errors] => Array
                    (
                        [http_request_failed] => Array
                            (
                                [0] => couldn't connect to host at xyz.com:80
                            )
            
                    )
            
                [error_data] => Array
                    (
                    )
            
            )
            */			
            // print '<pre>'; print_r($response); exit;
            
			// make sure the response came back okay
			if (empty($response)) return false;
			if (is_wp_error($response)) return false;
			$data = json_decode(wp_remote_retrieve_body($response));
			$data->key = trim( get_option( self::$key_option_name));

	 		set_transient( $cache_key, $data, 60*60 );
			return $status;	
		}
				
	}

}