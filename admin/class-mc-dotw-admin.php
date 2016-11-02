<?php
/**
 * MC_Dotw admin class.
 *
 * @package   MC_Dotw_Admin
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MC_Dotw_Admin
{
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct()
	{
		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = MC_Dotw::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Add custom functionality actions and filters.
		add_action( 'dotw_before_admin_page_display', array( $this, 'save_admin_form' ) );
		add_action( 'init', array( $this, 'init_includes' ) );
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
		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get plugin's options.
	 *
	 * @since     1.0.0
	 *
	 * @param   string  $option_name  Key of the option to retrieve. Default 'mc_dotw'.
	 *
	 * @return  array|string          The retrieved option.
	 */
	public function get_options( $option_name = 'mc_dotw' )
	{
        $dotw_options = get_option($option_name);

        return $dotw_options;
    }

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles()
	{
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), MC_Dotw::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts()
	{
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'suggest' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), MC_Dotw::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu()
	{
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'MC Dotw Settings', $this->plugin_slug ),
			__( 'MC Dotw', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Include the plugin's admin/shared class definitions.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function init_includes()
	{
		// require_once( 'includes/class-mc-dotw-admin-ajax.php' );
		// require_once( 'includes/class-mc-dotw-admin-cron.php' );
		// require_once( 'includes/class-mc-dotw-admin-wc-custom-fields.php' );
		// require_once( 'includes/class-mc-dotw-admin-widget.php' );

		new MC_Dotw_Admin_Ajax();
		new MC_Dotw_WC_Custom_Fields();
		$cron = MC_Dotw_Admin_Cron::get_instance();
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page()
	{
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links )
	{
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

    /**
     * Save form data submitted on the plugin's admin page.
     *
	 * @hooked 	 dotw_before_admin_page_display: './views/admin.php'
	 *
	 * @since    1.0.0
	 *
     * @param    void
     *
     * @return   array     A populated 53 weeks deals collection,
     *                     updated with the submitted form data.
     */
    public function save_admin_form()
    {
    	// Check user access rights.
        if ( ! current_user_can('manage_options') ) {
    		wp_die('You do not have sufficient permissions to access this page.');
    	}

        do_action( 'dotw_on_before_options_page' );

        // Flag the form submission status.
        $form_submitted = isset( $_POST['dotw_deals_form_submitted'] )
        	&& ! strcmp('Y', $_POST['dotw_deals_form_submitted']);

        if ( $form_submitted ) {
        	$dotw_options = $this->get_options();
	        $all_inputs_valid = true;

	        // Loop over each week of the year.
	        for ( $i=0; $i < MC_Dotw::YEAR_IN_WEEKS; $i++ ) {
	            $week_num = $i + 1;
	            $has_been_edited =
	            	isset( $_POST["dotw_deals_{$week_num}_wc_data_edited"] )
	                && 'Y' === $_POST["dotw_deals_{$week_num}_wc_data_edited"];

	            if ( $has_been_edited ) {
	                $deal = MC_Dotw_Deal::new_deal( $week_num );
	                $valid_post_data = $deal->validate_post_data();

	                if ( $valid_post_data ) {
	                    // Append this deal's persisted 'is_active' status to the data.
	                    $valid_post_data['is_active'] = $deal->is_active();

	                    // Populate the deal's $_data attribute with the new data.
	                    $data_was_set     = $deal->set_data( $valid_post_data );
	                    $all_inputs_valid = $data_was_set ? $all_inputs_valid : false;

	                    // Update the selected wc product if it hasn't just been reinitialized.
	                    if ( $data_was_set && ! $deal->is_blank() ){
	                        $deal->update_product();
	                    }
	                } else {
	                    $all_inputs_valid = false;
	                }

	                // Update the 'all deals' array with this deal's new data.
	                $dotw_options['deals'][$i] = $deal->get_data();
	            } // End if edited.
	        } // End for.

	        if ( $all_inputs_valid ) {
	            $endofweek_offset = isset( $_POST['dotw_deals_endofweek_offset'] ) && is_numeric( $_POST['dotw_deals_endofweek_offset'] )
	                    ? (string) $_POST['dotw_deals_endofweek_offset']
	                    : null;

	            $dotw_options['settings']['endofweek_offset'] = $endofweek_offset ? : $dotw_options['settings']['endofweek_offset'];
	            $dotw_options['last_updated'] = time();

	            update_option( 'mc_dotw', $dotw_options );
	        }

	        return $dotw_options['deals'];
        }

        do_action( 'dotw_options_page_before_content_display', $form_submitted );
	}

	/**
	 * Get a deal edition panel's aside HTML content.
	 *
	 * This method shows the pertinent backed up info about a deal's product sale's settings,
	 * in case the product's info has been temporarily modified by another deal of the week.
	 *
	 * Alternatively, shows a preview of such info whenever the user
	 * selects a new product for the deal.
	 *
	 * @since    1.0.1
	 *
	 * @param    MC_Dotw_Deal    $deal                  (Required) The edited deal of the week.
	 * @param    bool            $is_selection_preview  (Optional) Whether or not the aside content is for
	 *                                                  a selected product's preview. Default false.
	 *
	 * @return   string|null                            The deal's aside HTML content. Null if '$deal' is not valid.
	 */
	public function get_deal_aside_html( $deal, $is_selection_preview = false )
	{
		if ( ! $deal instanceof MC_Dotw_Deal )
			return;

		$html    = '';
		$product = $deal->get_the_product();

		// If the deal is active or shares the product it promotes with one or more other deals.
		if ( $deal->has_activity() || ( $product && count( $product->get_deals() ) > 1 ) ) {
			$week_num = $deal->get( 'week_num' );

			// If there is a product and A), it has more than 1 deal or, B), it's a new selection for the deal.
			if ( $product && ( count( $product->get_deals() ) > 1 || $is_selection_preview) ) {
				$nav_anchors            = array();

				// Get all product deals, get potential week_num of any active deal other than $deal
				// and associated to the product and, finally, get a filtered list of deals that does not
				// include the looped $deal nor the potential other active deal.
				// Then, add to displayed html if needed.
				$product_deals          = $product->get_deals();
				$elsewhere_active_num   = $deal->has_product_active_elsewhere()
					? $product->get_active_deal()['week_num']
					: 0;

				ksort( $product_deals, SORT_NUMERIC );

				// Set potential preview_backup update flag.
				$i = 0;

				foreach ( $product_deals as $deal_num => $deal_dataset ) {
					$css = '';
					$activity_text = '';

					// For the active deal only.
					if ( $deal_num == $elsewhere_active_num || ( $deal->is_active() && $deal_num == $week_num ) ) {
						$css = ' class="dotw-nav-current"';
						// $activity_text = ' (' . _x( 'Active', 'Deal Aside Wrapper: Deals list', $this->plugin_slug ) . ')';
					}

					// Populate the array of navigation anchors HTML string.
					$nav_anchors[] = '<a href="#dotw_deals_' . ($deal_num-1) . '_title"' . $css . '>' . $deal_num . $activity_text . '</a>';

					if ( $is_selection_preview && $i === 0 ) {
						// Populate previewed deal's 'backup' _data with the newly selected product's original
						// sale settings, found in the first (@see: $i) associated deal's dataset.
						$preview_backup = array(
							"regular_price" => $deal->get( 'regular_price' ),
							"sale_price"    => $deal_dataset['backup']['sale_price'],
							"date_from"     => $deal_dataset['backup']['date_from'],
							"date_to"       => $deal_dataset['backup']['date_to']
						);
						$deal->set( 'backup', $preview_backup );
					}
					$i++;
				}

				if ( count( $nav_anchors ) ) {
					$html .= '<p id="dotw_deal_' . esc_html( $week_num ) . '_related_deals">';
					$html .= esc_html( _n( 'Related deal:', 'Related deals:', count( $nav_anchors ), $this->plugin_slug ) );
					$html .= ' ' . implode( ', ', $nav_anchors );
					$html .= '</p>';
				}
			}

			// Maybe get the HTML content for the product's original sale settings.
			if ( $deal->has_activity() && ( ! $deal->is_blank() || $is_selection_preview ) ) {
				$sale_price_text = $deal->get( 'sale_price' )
					? __( 'Usual sale price: ', $this->plugin_slug ) . $deal->get( 'sale_price' ) . ' ' . get_woocommerce_currency_symbol()
					: _x( 'None', 'No price', $this->plugin_slug );

				$date_from_text  = $deal->get( 'date_from' )
					? __( 'Usual sale start date: ', $this->plugin_slug ) . $deal->get( 'date_from' )
					: __( 'Usual sale start date: ', $this->plugin_slug ) . _x( 'None', 'No date', $this->plugin_slug );

				$date_to_text    = $deal->get( 'date_to' )
					? __( 'Usual sale end date: ', $this->plugin_slug ) . $deal->get( 'date_to' )
					: __( 'Usual sale end date: ', $this->plugin_slug ) . _x( 'None', 'No date', $this->plugin_slug );

				$html .= '<p id="dotw_deal_' . esc_html( $week_num ) . '_usual_sale_price">'     . esc_html( $sale_price_text ) . '</p>';
				$html .= '<p id="dotw_deal_' . esc_html( $week_num ) . '_usual_sale_date_from">' . esc_html( $date_from_text )  . '</p>';
				$html .= '<p id="dotw_deal_' . esc_html( $week_num ) . '_usual_sale_date_to">'   . esc_html( $date_to_text )    . '</p>';
			}
		}

		return $html;
	}
}
