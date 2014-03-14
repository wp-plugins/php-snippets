<?php
/**
* EDD Licence Class
* ACtivate And Check License key
**/
class PHP_License {

	public static $edd_store = 'http://wpcctm.com/'; // store_url
	public static $plugin = 'PHP Snippets'; // item name from store

	public function __construct() 
	{	
		$this->edd_register_option();
		$this->edd_activate_license();
	}

	/**
	* Activate License Page
	* Display License Filed and Activate button
	**/
	public static function activate_license_page() {
		$license 	= get_option( 'php_edd_license_key' );
		$status 	= get_option( 'php_edd_license_status' );
		?>
		<div class="wrap">
			<h2><?php _e('Plugin '.self::$plugin.' License Options'); ?></h2>
			<form method="post" action="options.php">
			
				<?php settings_fields('php_license'); ?>
				
				<table class="form-table">
					<tbody>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('License Key'); ?>
							</th>
							<td>
								<input id="php_edd_license_key" name="php_edd_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
								<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
						
							</td>
						</tr>
						<?php if( false !== $license ) { ?>
							<tr valign="top">	
								<th scope="row" valign="top">
									<?php _e('Activate License'); ?>
								</th>
								<td>
									<?php if( $status !== false && $status == 'valid' ) { ?>
										<span style="color:green;"><?php _e('active'); ?></span>
									<?php } else {
										wp_nonce_field( 'edd_nonce', 'edd_nonce' ); ?>
										<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>	
				
			
			</form>
		<?php
	}

	public static function inactive_page() {
		?>
		<div id="php-warning" class="updated fade"><p><strong>PHP Snippets is almost ready.</strong> You must enter your License key for it to work and show PHP Snippets Setting.</p></div>
		<?php 
	}

	/**
	* edd_register_option
	**/
	public static function edd_register_option() {
		// creates our settings in the options table
		register_setting('php_license', 'php_edd_license_key', array('PHP_License','edd_sanitize_license'));
	}

	
	/**
	* activate_license_menu
	* Add Plugin Licence Menu
	* This is a prepared function to add Custom Menu for the plugin
	* Usage: optional
	* They can add a custom menu as a sub page for Activate License
	**/
	public static function activate_license_menu() {
		add_plugins_page( 'Activate '.self::$plugin.' License', 'Activate ' .self::$plugin. ' License', 'administrator', 'activate-' .strtolower(str_replace(' ', '_', self::$plugin)). '-license', array('PHP_License','activate_license_page') );
	}

	/**
	* edd_sanitize_license
	**/
	public static function edd_sanitize_license( $new ) {
		$old = get_option( 'php_edd_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'php_edd_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	* edd_activate_license
	**/
	public static function edd_activate_license() {
 
		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_activate'] ) ) {

	 
			// run a quick security check 
		 	if( ! check_admin_referer( 'edd_nonce', 'edd_nonce' ) ) 	
				return; // get out if we didn't click the Activate button
	 
			// retrieve the license from the database
			$license = trim( get_option( 'php_edd_license_key' ) );
				
	 
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
				'url'       => home_url()
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, self::$edd_store ) );
	 
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
	 
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "active" or "inactive"
	 
			update_option( 'php_edd_license_status', $license_data->license );
	 
		}
	}

	/**
	* edd_check_license
	* cache the result using set_transient
	**/
	public static function edd_check_license() {	
		$cache_key = 'edd_license-'.strtolower(str_replace(' ', '_', self::$plugin));
		$data = get_transient( $cache_key );
		$key_old = trim( get_option( 'php_edd_license_key' ) );

		if ($data && $key_old == $data->key) {
			return $data;
			exit;
		}
		// retrieve the license from the database
		$license = trim( get_option( 'php_edd_license_key' ) );
			
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'check_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
			'url'       => home_url()
		);
	
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, self::$edd_store ) );
 
		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;
		$data = json_decode( wp_remote_retrieve_body( $response ) );
		$data->key = trim( get_option( 'php_edd_license_key' ) );

 		set_transient( $cache_key, $data, 60*60 );
		return $data;			
	}

}
//register setting
add_action('admin_init', function(){
	new PHP_License();
});