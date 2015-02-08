<?php
/**
 * Check license
 * in version 1.1, there is an effort to fail more gracefully if the remote server is having trouble or there
 * are troubles with SSL negotiation.
 */
namespace PhpSnippets;

class License {

	public static $store_url = 'https://craftsmancoding.com/products/'; // store_url
	public static $product_url = 'https://craftsmancoding.com/products/downloads/php-snippets/';
	public static $plugin = 'PHP Snippets'; // item name from store
    public static $key_option_name = 'php_snippets_license_key';
    public static $status_option_name = 'php_snippets_license_status'; // e.g. valid
    public static $license_check_frequency_in_days = 7;

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
	 * Handles if/when a user changes their license key
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
	 * @param string $license_key
	 *
	 * @return bool if activation succeeded or false if it failed
	 */
	public static function activate($license_key) {

        $license_key = trim($license_key);

		// ~ add_option / set_option
        update_option(self::$key_option_name, $license_key);

		if ($response = self::remote_check_license($license_key,'activate_license'))
		{
			// We append the license key to the response to store it for comparison later
			$response->key = $license_key;
			set_transient( $cache_key, $response, 60*60*24*self::$license_check_frequency_in_days );
			$status = $response->license; // *facepalm* -- status (e.g. "valid") is stored in the 'license' key.
			update_option(self::$status_option_name, $status);

			if($response->success) {
				return true; // <-- legit activation here
			}
		}
		// Remote request failed!
		// We will assume it's good and check again tomorrow
		else
		{
			$response = new \stdClass();
			$response->key = $license_key;
			set_transient( $cache_key, $response, 60*60*24*1 );
			return true;
		}

		return false;
	}

	/**
	 * Check that the license is valid.
	 * cache the result using set_transient
	 *
	 * @return string status e.g. 'valid'
	 */
	public static function check() {	
		$license_key  = trim( get_option(self::$key_option_name));
		$status       = get_option(self::$status_option_name);
		$cache_key    = strtolower(str_replace(' ', '_', self::$plugin));
		$cached_data  = get_transient($cache_key); // returns false if expired

		if ($cached_data && $license_key == $cached_data->key) {
			return $status;
		} 
		elseif($response = self::remote_check_license($license_key,'check_license'))
		{
			// We append the license key to the response to store it for comparison later
			$response->key = $license_key;
	 		set_transient( $cache_key, $response, 60*60*24*self::$license_check_frequency_in_days );
			$status = $response->license; // *facepalm* -- status (e.g. "valid") is stored in the 'license' key.
			update_option(self::$status_option_name, $status);
			return $status;
		}
		// Remote request failed!
		// We punt: assume the license is good and check again tomorrow
		else
		{
			$response = new \stdClass();
			$response->key = $license_key;
			set_transient( $cache_key, $response, 60*60*24*1 );
			return $status;
		}
				
	}

	/**
	 * Call the remote server to validate the given license key.
	 * See http://docs.easydigitaldownloads.com/article/384-software-licensing-api
	 *
	 * We use simple file_get_contents() because curl is not
	 * always available and wp_remote_get() was problematic re SSL certificates and CloudFlare. (WTF?)
	 * Format of 'activate_license' response is like this:
	 *
	 * (
	 * 	[success] => 1
	 * 	[license_limit] => 0
	 * 	[site_count] => 1
	 * 	[activations_left] => unlimited
	 * 	[license] => valid
	 * 	[item_name] => PHP Snippets
	 * 	[expires] => YYYY-MM-DD HH:ii:ss
	 * 	[payment_id] => xxx
	 * 	[customer_name] => XXXX YYYY
	 * 	[customer_email] => xxxx@mail.com
	 * )
	 *
	 * Format of 'check_license' response is like this:
	 * (
	 *	[license] => valid
	 *	[item_name] => PHP Snippets
	 *	[expires] => 2015-12-09 14:56:54
	 *	[payment_id] => xxx
	 *	[customer_name] => XXXX YYYY
	 *	[customer_email] => xxxx@mail.com
	 * )
	 *
	 * @param string $license_key
	 * @param string $action activate_license | check_license
	 *
*@return mixed
	 */
	public static function remote_check_license($license_key, $action='check_license')
	{
		$api_params = array(
			'edd_action'=> $action,
			'license' 	=> $license_key,
			'item_name' => urlencode( self::$plugin ), // the unique name of our product in EDD,
			'url'       => home_url(),
			'rand' => uniqid() // cache-busting
		);

		$endpoint = add_query_arg( $api_params, self::$store_url );

		$response = @file_get_contents($endpoint);

		if ($response == false)
		{
			error_log('['.self::$plugin.'] There was a problem accessing the remote server: '.self::$store_url);
			return false;
		}

		$response = json_decode($response); // Decode as Object
		if (!is_object($response))
		{
			error_log('['.self::$plugin.'] The response from the remote server was not JSON: '.self::$store_url);
			return false;
		}
		// print 'Response: <pre>'; print_r($response); exit;

		return $response;
	}

}