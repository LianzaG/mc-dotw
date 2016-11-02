<?php
/**
 * Ajax requests handler. Amongst other things, sends an ajax response containing a list of WC
 * products autocomplete suggestions. Called as AJAX callback by 'suggest.js' when editing input
 * fields in the plugin settings page.
 *
 * @see       https://snippets.webaware.com.au/snippets/autocomplete-in-wordpress-plugin-admin/]
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

class MC_Dotw_Admin_Ajax
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();

        add_action('wp_ajax_dotw_deals_suggest_options', array( $this, 'suggest_wc_products_options_list' ) );
        add_action('wp_ajax_dotw_deals_option_meta',     array( $this, 'send_selected_option_meta' ) );
    }

    /**
     * Send response to the deal edition form's auto-suggest AJAX request, containing
     * suggestions of woocommerce products matching the characters typed so far.
     *
     * @since    1.0.0
     *
     * @param    void
     *
     * @return   void  The Ajax response needs no return value. Results are echoed out
     *                 and the process is then killed using wp_die().
     */
    public function suggest_wc_products_options_list()
    {
        $req_search_query = $_GET['q'];

        $loop = new WP_Query( array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            's'              => esc_attr( $req_search_query )
            )
        );

        if ( $loop->have_posts() ) : echo 'tt' ;?>
            <datalist id="dotw_deals_suggest_list">
                <?php while ( $loop->have_posts() ) : $loop->the_post();
                    $product = wc_get_product($loop->post->ID);
                    $option_response = json_encode(
                        array(
                            'title'      => $product->post->post_title,
                            'product_id' => $loop->post->ID
                        )
                    ); ?>
                    <option><?php esc_html_e( $option_response );?></option>
                <?php endwhile; ?>
            </datalist>
        <?php else :
            echo _x( 'No products found', 'Deals Admin: no AJAX search result', $this->plugin_slug );
        endif;

        wp_reset_postdata();

        die();
    }

    /**
     * Send Ajax response to the request sent on deal's title form field change event.
     * The response contains the id and all relevant pricing settings of/for the selected product.
     *
     * @since    1.0.0
     *
     * @param    void
     *
     * @return   void  The Ajax response needs no return value. Results are echoed out
     *                 and the process is then killed using wp_die().
     */
    public function send_selected_option_meta()
    {
        // Gather all relevant product related infos from the AJAX GET request.
        $meta = get_post_meta( esc_attr( $_GET['product_id'] ) );

        // Set product sale's wc catalog settings.
        $res = array(
            'regular_price' => esc_attr( $meta['_regular_price'][0] ),
            'sale_price'    => esc_attr( $meta['_sale_price'][0] ),
            'hot_price'     => '',
            'date_from'     => strftime('%Y-%m-%d', $meta['_sale_price_dates_from'][0]),
            'date_to'       => strftime('%Y-%m-%d', $meta['_sale_price_dates_to'][0]),
        );
        // Make sure dates are valid.
        $res['date_from'] = (1000*1000) < $meta['_sale_price_dates_from'][0] ? $res['date_from'] : '';
        $res['date_to']   = (1000*1000) < $meta['_sale_price_dates_to'][0]   ? $res['date_to']   : '';

        // Prepare arguments to get the deal's aside.
        $aside_args = array(
            'product_meta' => $meta,
            'product_id'   => esc_attr( $_GET['product_id'] ),
            'week_num'     => esc_attr( $_GET['week_num'] ),
            'title'        => esc_attr( $_GET['title'] )
        );
        // Get the deal's aside wrapper's html content.
        $res['aside_html'] = $this->get_deal_tmp_aside_html( $aside_args );

        echo json_encode($res);

        wp_die();
    }

    /**
     * Merge a deal's data with the newly selected product's data.
     *
     * @since    1.0.1
     *
     * @param    MC_Dotw_Deal    &$deal    (Required) The deal being merged, passed by reference.
     * @param    array           $args     (Required) Associative array.
     *
     * @return   void
     */
    protected function merge_deal_product_selection( &$deal, $args )
    {
        // Set the deal's product dotw object.
        $deal->set( 'product'   , new MC_Dotw_Product( $args['product_id'] ) );

        /*
         * Set the deal's dataset. Avoid calling the deal's set_data() method
         * in order to prevent _before_set_data() from being triggered.
         */
        $deal->set( 'product_id', $args['product_id'] );
        $deal->set( 'title'     , $args['title'] );
        $deal->set( 'backup'    , array(
            'regular_price' => esc_attr( $args['product_meta']['_regular_price'][0] ),
            'sale_price'    => '',
            'date_from'     => '',
            'date_to'       => '',
        ));
    }

    /**
     * Get HTML content for a deal's aside preview infos, after a new product has been selected.
     *
     * @since   1.0.1
     *
     * @param   array  $args  (Required) Associative array. Holds the AJAX request's useful parameters
     *                        as well as the product's sale settings metadata. Keys are listed below.
     *                            'product_meta' => Sale settings metadata
     *                            'product_id'   => ID of the selected product
     *                            'week_num'     => The edited deal's week number
     *                            'title'        => The selected product's title
     *
     * @return  string        The deal's aside HTML content.
     */
    protected function get_deal_tmp_aside_html( $args )
    {
        // Validate args.
        if ( ! isset( $args['week_num'] ) || $args['week_num'] < 1 || $args['week_num'] > MC_Dotw::YEAR_IN_WEEKS )
            return;
        if ( ! isset( $args['product_meta'] ) || ! is_array( $args['product_meta'] ) )
            return;
        if ( ! isset( $args['product_id'] ) )
            return;

        // Get a deal object for the AJAX request's deal and merge it's data with the newly selected product's data.
        $deal = new MC_Dotw_Deal( (int) $args['week_num'] );
        $this->merge_deal_product_selection( $deal, $args );

        // Get and return the populated HTML template from our dotw admin class.
        return MC_Dotw_Admin::get_instance()->get_deal_aside_html( $deal, true );
    }
}
