<?php
/**
 * MC_Dotw
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

/**
 * MC_Dotw class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-mc-dotw-admin.php`
 *
 * @package MC_Dotw
 * @author  Julien Bosuma <jbosuma@gmail.com>
 */
class MC_Dotw {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.2';

	/**
	 * Number of weeks in a year (rounded up).
	 *
	 * @since   1.0.0
	 *
	 * @var     integer
	 */
	const YEAR_IN_WEEKS = 53;

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'mc-dotw';


	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct()
	{
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'init', array( $this, 'init_shortcode' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug()
	{
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide )
	{
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide )
	{
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id )
	{
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids()
	{
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate()
	{
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

	    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

	    if ( check_admin_referer( "activate-plugin_{$plugin}" ) ) {
			// Add plugin option.
			if ( ($option = get_option('mc_dotw')) === false ) {
				$option = array(
					"deals"        => MC_Dotw_Deal::get_default_datasets(), // Array of deals dataset arrays.
					"settings"     => array(								// Global settings.
					    "endofweek_offset"            => '1', 				// Set last day of the week (1 => sunday, 2 => saturday, etc.).
						"isset_wc_product_deals_nums" => '', 				// Flag for metadata setup.
						"widget"					  => array( 			// Widget options.
							"title" 		=> '', 											// Widget's default title.
							"slick" 		=> MC_Dotw_Widget::get_slick_init_defaults(),   // Slick layout defaults.
						),
					),
					"last_updated" => time(),
				);

				add_option( 'mc_dotw', $option );
			}

			// Add products metadata.
			if ( empty($option['settings']['isset_wc_product_deals_nums']) ) {
				$products = get_posts( array(
					'post_type'   => 'product',
					'numberposts' => -1
				));

				if ( count( $products ) ) {
					$metakey = 'dotw_wc_product_deal_nums';

					// Init plugin metakey for each woocommerce product (if not set).
					foreach ( $products as $product ) {
						if ( get_post_meta( $product->ID, $metakey, true ) === '' ) {
							update_post_meta( $product->ID, $metakey, array() );
						}
					}
				}

				$option['settings']['isset_wc_product_deals_nums'] = 'yes';
				update_option( 'mc_dotw', $option );
			}
	    }
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate()
	{
		if ( ! current_user_can( 'activate_plugins' ) ) {
	        return;
		}

	    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

	    if ( check_admin_referer( "deactivate-plugin_{$plugin}" ) ) {
			// Get the current deal of the week and, if it is linked to a product,
			// restore the product's original pricing data.
			$dotw = new MC_Dotw_Deal();

			if ( ! $dotw->is_blank() ){
				$options = MC_Dotw_Admin::get_instance()->get_options();

				// Flag the deal of the week as inactive in the plugin's options.
				$dotw->set( 'is_active', false );
				$options['deals'][$dotw->get( 'week_num' )-1] = $dotw->get_data();

				// Restore the product's backed up pricing data.
				$dotw->update_product( true );

				// Store modified dotw options state.
				update_option( 'mc_dotw', $options );
			}
	    }
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );

		// Slick's CSS.
		if ( ! wp_style_is( 'slick', 'enqueued' ) ) {
			// wp_register_style( 'slick', plugins_url( 'assets/js/slick/slick.css', __FILE__ ), array(), self::VERSION, 'all' );
			wp_register_style( 'slick', '//cdn.jsdelivr.net/jquery.slick/1.5.5/slick.css', array(), self::VERSION, 'all' );
			wp_enqueue_style( 'slick' );

			wp_register_style( 'slick-theme', '//cdn.jsdelivr.net/jquery.slick/1.5.5/slick-theme.css', array(), self::VERSION, 'all' );
			wp_enqueue_style( 'slick-theme' );
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );

		// Slick.js
		if ( ! wp_script_is( 'slick', 'enqueued' ) ) {
			// wp_register_script( 'slick', plugins_url( 'assets/js/slick/slick.min.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
			wp_register_script( 'slick', '//cdn.jsdelivr.net/jquery.slick/1.5.5/slick.min.js', array( 'jquery' ), self::VERSION, true );
			wp_enqueue_script( 'slick' );
		}
	}

	/**
	 * Init plugin shortcode.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function init_shortcode()
	{
		// require_once( 'includes/class-mc-dotw-shortcode.php' );
		new MC_Dotw_Shortcode();
	}

	/**
	 * Get the objects (or the `$key` attributes) of all non-empty deals, or
	 * only those of a time-filtered subset of non-empty deals.
	 *
	 * @since    1.0.2
	 *
	 * @param    string  $key          	(Optional) The object's attribute to return for each deal.
	 *                                 	The object itself is returned instead, if no argument is passed.
	 *                                 	Default '' (empty string).
	 * @param    string  $cardinality  	(Optional) If either 'past' or 'future' is passed as argument,
	 *                                 	the results will be filtered to only include past or future deals.
	 *                                 	Default '' (empty string).
	 * @param    bool    $current      	(Optional) Whether or not to include the current week's deal.
	 *                                 	Default true.
	 * @param 	 integer $limit 	   	(Optional) Maximum number of deals to return. Default 0.
	 *
	 * @param 	 array 	 $range 		(Optional) Associative array of min. and max. week numbers to observe
	 *                           		when returning deals: {
	 *                                 		'week_num_min' => '',
	 *                                 		'week_num_max' => '',
	 *                                 	}.
	 *                                 	Useful when not applying time-filtering (with $cardinality), in order to
	 *                                 	adjust and center the returned list around the current/desired week.
	 *
	 * @return   array                 	Array of either MC_Dotw_Deal objects or appropriate attribute values,
	 *                                 	if a valid `$key` was supplied.
	 */
	public function get_set_deals(
		$key = '',
		$cardinality = '',
		$current = true,
		$limit = 0,
		$range = array(
			'week_num_min' => '',
			'week_num_max' => '',
			)
	) {
		// Init time-filtered deals containers.
		$past   = array();
		$future = array();

		// Conditionals. Set state.
		$load_past = ! $cardinality || ( 'past' == $cardinality );
		$load_futr = ! $cardinality || ( 'future' == $cardinality );

		// 1 - Take every deal of the year.
		foreach ( MC_Dotw_Admin::get_instance()->get_options()['deals'] as $deal_dataset ) {
			$deal = new MC_Dotw_Deal( $deal_dataset['week_num'] );

			// 2 - If not empty,
			if ( ! $deal->is_blank() ) {
				/*
				 * 3 - Avoid including out-of-range deals if min/max week_nums have been specified
				 *     using the `$range` argument.
				 */
				if(
					( ! $range['week_num_min'] || $range['week_num_min'] <= $deal->get( 'week_num' ) )
				 && ( ! $range['week_num_max'] || $range['week_num_max'] >= $deal->get( 'week_num' ) )
				) {
					/*
					 * 4 - See if they fit in any of the past/current/future time-filtered containers.
					 *
					 * 5 - Use 'week numbers as keys' for the container arrays being built.
					 * 6 - If a specific '$key' was requested, load that $key's value.
					 *     Otherwise, load the deal itself.
					 */
					if ( $load_past && $deal->is_past() ) {
						$past[$deal->get( 'week_num' )] = $key ? $deal->get( $key ) : $deal;
					}

					if ( $current && $deal->is_current() ) {
						// No time-filtered container is needed for the current deal (there's only one…).
						// We'll load `$cur_deal` by itself, below, when populating `$set_deals`.
						$cur_deal = $key ? $deal->get( $key ) : $deal;
					}

					if ( $load_futr && $deal->is_future() ) {
						$future[$deal->get( 'week_num' )] = $key ? $deal->get( $key ) : $deal;
					}
				}
			}
		}

		// 7a - Results array. Load.
		$set_deals = array();

		if ( $load_past ) { $set_deals   = $past; }
		if ( $current )   { $set_deals[$cur_deal->get( 'week_num' )] = $cur_deal; }
		if ( $load_futr ) { $set_deals   = array_merge( $set_deals, $future ); }

		//  7b - Results array. Show closest deals first when not listing any future deal.
		if ( ! $load_futr ) {
			$set_deals = array_reverse( $set_deals );
		}

		/*
		 * 8 - Apply $limit if needed.
		 */

		// Sanitize supplied $limit argument.
		$limit = MC_Dotw::YEAR_IN_WEEKS < $limit ? MC_Dotw::YEAR_IN_WEEKS : $limit;
		$limit = 0  > $limit ? 0  : $limit;

		// If there's a max. number of deals specified and exceeded.
		if ( $limit && count( $set_deals ) > $limit ) {
			$set_deals = array_chunk( $set_deals, $limit )[0];
		}

		return $set_deals;
	}
}
