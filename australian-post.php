<?php
/* @wordpress-plugin
 * Plugin Name:       Australia Post WooCommerce Shipping Method
 * Plugin URI:        http://waseem-senjer.com/
 * Description:       WooCommerce Australian Post Shipping Method.
 * Version:           1.1.0
 * Author:            Waseem Senjer
 * Author URI:        http://waseem-senjer.com
 * Text Domain:       australian-post
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/wsenjer/Links-Replacer
 */


$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(in_array('woocommerce/woocommerce.php', $active_plugins)){


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