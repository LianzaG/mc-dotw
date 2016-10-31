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
        add_action('wp_ajax_dotw_deals_option_meta', array( $this, 'send_selected_option_meta' ) );
    }

    /**
     * Send response to the deal edition form's auto-suggest AJAX request, containing
     * suggestions of woocommerce products matching the characters typed so far.
     *
     * @param   void
     *
     * @return  void  The Ajax response needs no return value. Results are echoed out
     *                and the process is then killed using wp_die().
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

        if ( $loop->have_posts() ) : ?>
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
     * Send Ajax response to the request sent on form field deal's title change event.
     * The response contains the id and all relevant pricing settings of/for the selected product.
     *
     * @param   void
     *
     * @return  void  The Ajax response needs no return value. Results are echoed out
     *                and the process is then killed using wp_die().
     */
    public function send_selected_option_meta()
    {
        $deal_title = esc_html( $_GET['title'] );
        // Gather all relevant product related infos.
        $post_id = get_page_by_title( $deal_title, OBJECT, 'product' )->ID;
        $meta    = get_post_meta( $post_id );

        $res = array(
            'regular_price' => esc_html( $meta['_regular_price'][0] ),
            'sale_price'    => esc_html( $meta['_sale_price'][0] ),
            'hot_price'     => '',
            'date_from'     => strftime('%Y-%m-%d', $meta['_sale_price_dates_from'][0]),
            'date_to'       => strftime('%Y-%m-%d', $meta['_sale_price_dates_to'][0]),
        );

        $res['date_from'] = 1000*1000 < $meta['_sale_price_dates_from'][0] ? $res['date_from'] : '';
        $res['date_to'] = 1000*1000 < $meta['_sale_price_dates_to'][0] ? $res['date_to'] : '';

        echo json_encode($res);

        wp_die();
    }
}
