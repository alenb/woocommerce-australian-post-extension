<?php
/* @wordpress-plugin
 * Plugin Name:       Australia Post WooCommerce Extension
 * Plugin URI:        https://waseem-senjer.com/
 * Description:       WooCommerce Australian Post Shipping Method.
 * Version:           1.4.0
 * Author:            Waseem Senjer
 * Author URI:        https://waseem-senjer.com
 * Text Domain:       australian-post
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/wsenjer/woocommerce-australian-post-extension
 */

define('AUSPOST_LITE_URL', plugin_dir_url(__FILE__));

// deactivate the pro version
if(auspost_is_auspost_pro_active()){
	function auspost_deactivate_pro_version() {
	  deactivate_plugins( 'woocommerce-australia-post-extension-pro/class-australian-post.php' );
	}
	add_action( 'admin_init', 'auspost_deactivate_pro_version' );
}

if(auspost_is_woocommerce_active()){
		add_filter('woocommerce_shipping_methods', 'add_australian_post_method');
		function add_australian_post_method( $methods ){
			$methods[] = 'WC_Australian_Post_Shipping_Method';
			return $methods; 
		}

		add_action('woocommerce_shipping_init', 'init_australian_post');
		function init_australian_post( ){
			require 'class-australian-post.php';
		}
}


function auspost_is_woocommerce_active(){
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() )
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	
	return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}

function auspost_is_auspost_pro_active(){
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() )
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	
	return in_array( 'woocommerce-australia-post-extension-pro/class-australian-post.php', $active_plugins ) || array_key_exists( 'woocommerce-australia-post-extension-pro/class-australian-post.php', $active_plugins );
}