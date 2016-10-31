<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
}

// check_admin_referer( 'bulk-plugins' );

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


function dotw_uninstall_remove_metakey()
{
	$products = get_posts( array(
		'post_type'   => 'product',
		'numberposts' => -1
	));

	if ( count( $products ) ) {
		$metakey = 'dotw_wc_product_deal_nums';

		// Delete plugin metakey for each woocommerce product.
		foreach ( $products as $product ) {
			delete_post_meta( $product->ID, $metakey );
		}
	}
}

delete_option('mc_dotw');
dotw_uninstall_remove_metakey();

