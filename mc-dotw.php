<?php
/**
 * MC Dotw
 *
 * A plugin to create a well-organized WooCommerce products weekly promotions system.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 *
 * @wordpress-plugin
 * Plugin Name:       MC Dotw
 * Plugin URI:
 * Description:       A plugin to create a well-organized WooCommerce products weekly promotions system.
 * Version:           0.0.1
 * Author:            Julien Bosuma
 * Author URI:        http://lianzadesign.com/
 * Text Domain:       mc-dotw
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI:
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Follow wp's locale and timezone.
setlocale(LC_TIME, get_locale() );
date_default_timezone_set( get_option('timezone_string') );

// Include the plugin dependencies autoloader.
require( 'vendor/autoload.php');

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-mc-dotw.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'MC_Dotw', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MC_Dotw', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'MC_Dotw', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-mc-dotw-admin.php' );
	add_action( 'plugins_loaded', array( 'MC_Dotw_Admin', 'get_instance' ) );

}