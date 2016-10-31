<?php
/**
 * Deal of th Week's custom fields generator.
 * Registers new custom meta boxes for wooCommerce 'product' edit page.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MC_Dotw_WC_Custom_Fields
{
    /**
     * Constructor.
     */
    function __construct()
    {
    	$this->plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();

        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_dotw_custom_fields' ) );
        add_action( 'woocommerce_process_product_meta', 				array( $this, 'save_dotw_custom_fields' ) );
    }

    /**
     * Add dotw custom metabox.
     *
     * @since    1.0.0
     *
     * @param    void
     *
     * @return   void
     */
    public function add_dotw_custom_fields()
    {
		global $woocommerce, $post;

		// Get the week numbers of all MC_Dotw_Deal's linked to this product.
		$linked_deals_week_nums = new MC_Dotw_Product( $post->ID )->get( 'deal_nums' );
		sort( $linked_deals_week_nums, SORT_NUMERIC );

		// Set the [week_nums] custom field's text content.
		$week_nums_txt = count( $linked_deals_week_nums )
			? implode(', ', $linked_deals_week_nums )
			: __( 'This product is not linked.', $this->plugin_slug );

		// Set up and, maybe, filter the [week_nums] custom field.
		$week_nums_cf = array(
			'id'          => '_dotw_wc_product_deal_nums',
			'label'       => _n( 'Deal of the Week #', 'Deals of the Week #', count( $linked_deals_week_nums ), $this->plugin_slug ),
			'placeholder' => esc_attr( $week_nums_txt ),
			'desc_tip'    => 'true',
			'description' => __( 'This read-only field allows you to monitor the product\'s deal of the week associations.'
							   . ' Values entered here won\'t be saved.'
							   , $this->plugin_slug )
		);
		$week_nums_cf = apply_filters( 'dotw_wc_product_deal_nums_cf', $week_nums_cf, $linked_deals_week_nums, $post );

		// Start HTML output.
		do_action( 'dotw_wc_product_before_add_custom_fields', $post, $linked_deals_week_nums, $week_nums_cf );
		echo '<div class="options_group">';

			// Output the [week_nums] custom field's HTML.
			woocommerce_wp_text_input( $week_nums_cf );
			do_action( 'dotw_wc_product_add_custom_fields', $post, $linked_deals_week_nums );

		echo '</div>';
		do_action( 'dotw_wc_product_after_add_custom_fields', $post, $linked_deals_week_nums );
    }

	/**
	 * Data is not saved on this page. It is presented for information purposes only.
	 * Settings should be modified using the plugin's admin options page but, still,
	 * this action hook enables templates/plugins to go for another course of action.
	 *
	 * @since    1.0.0
	 *
	 * @param    $post_id 	Saved post's id.
	 *
	 * @return   void
	 */
    public function save_dotw_custom_fields( $post_id )
    {
    	do_action(
    		'dotw_wc_product_save_custom_fields',
    		$post_id,
    		$_POST['_dotw_wc_product_deal_nums'],
    		$_POST
    	);
    }
}
