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
 * Version:           1.0.3
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

define( 'DOTW_ROOT', plugin_dir_path( __FILE__ ) );

// Include vendor dependencies autoloader.
require( 'vendor/autoload.php');
// Include shared/admin/public class definitions.
require_once( DOTW_ROOT . '/includes/class-mc-dotw-deal.php' );
require_once( DOTW_ROOT . '/includes/class-mc-dotw-product.php' );
require_once( DOTW_ROOT . '/admin/includes/class-mc-dotw-admin-ajax.php' );
require_once( DOTW_ROOT . '/admin/includes/class-mc-dotw-admin-cron.php' );
require_once( DOTW_ROOT . '/admin/includes/class-mc-dotw-admin-wc-custom-fields.php' );
require_once( DOTW_ROOT . '/admin/includes/class-mc-dotw-admin-widget.php' );
require_once( DOTW_ROOT . '/public/includes/class-mc-dotw-shortcode.php' );
/**
 * @todo Pass the get_options() method to MC_Dotw instead of calling MC_Dotw_Admin
 *       Would allow to include MC_Dotw_Admin in the admin conditional, not loading it on front-end.
 */
require_once( DOTW_ROOT . 'admin/class-mc-dotw-admin.php' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( DOTW_ROOT . 'public/class-mc-dotw.php' );
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
 * If you don't want to include Ajax within the dashboard anymore, change the following
 * conditional to:
 *
 * if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
 *    ...
 * }
 *
 * The conditional above is intended to give the lightest footprint possible.
 */
if ( is_admin() ) {

	add_action( 'plugins_loaded', array( 'MC_Dotw_Admin', 'get_instance' ) );
}
