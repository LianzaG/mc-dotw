<?php
/**
 * The MC_Dotw Shortcode.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MC_Dotw_Shortcode
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin = MC_Dotw::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        add_shortcode( 'mc_dotw', array( $this, 'shortcode' ) );
        add_shortcode( 'dotw_deal_of_the_week', array( $this, 'shortcode' ) );
        add_shortcode( 'mc_dotw_inline', array( $this, 'shortcode_inline' ) );
    }

    /**
     * Display the deal of the requested week.
     *
     * If a valid 'week_num' is passed in the shortcode's attributes, the method
     * displays the deal of the week number #<week_num>. Defaults to displaying
     * the current week's deal.
     *
     * @since    1.0.0
     *
     * @param   array  $atts  (Optional) Array of optional shortcode setting attributes.
     *
     * @return  string        Returns/echoes out the HTML output displaying the deal of the week.
     */
    public function shortcode( $atts=array() )
    {
        $atts = shortcode_atts( array(
            'week_num' => null,
            'skin'     => ''
            ), $atts );

        $deal = apply_filters( 'dotw_shortcode_get_current_deal', new MC_Dotw_Deal( $atts['week_num'] ), $atts );

        // Switch process if deal is empty.
        if ( $deal->is_blank() ) {
            return apply_filters(
                'dotw_shortcode_get_blank_deal_html',
                $this->get_blank_deal_html( $atts['week_num'] ),
                $deal
            );
        }

        // -- More displayed data references.
        // Dates data.
        $calendR_objs  = $deal->get_calendar_objects();

        // Displayed price data.
        $display_price = $deal->get( 'hot_price' ) > 0
            ? $deal->get( 'hot_price' )
            : $deal->get( 'sale_price' );

        $display_price = apply_filters( 'dotw_shortcode_get_display_price', $display_price, $deal );

        // -- Content elements.
        // Title.
        $brand = false === strpos( $deal->get( 'title' ), $deal->get( 'product_brand' )->name )
            ? $deal->get( 'product_brand' )->name . ' '
            : '';

        $title = $brand . $deal->get( 'title' ) . ' ' . _x( 'at', 'Price', $this->plugin_slug ) . ' ' . $display_price . '€ ' . _x( 'instead of', 'Sale price', $this->plugin_slug ) . ' ' . $deal->get( 'regular_price' ) . '€';
        $title = apply_filters( 'dotw_shortcode_title_content', $title, $deal, $display_price );

        // Dates description text
        $text_dates = _x( 'From ', 'Time period start date' ,$this->plugin_slug ) . ' ' . $calendR_objs['start']->format( 'd' ) . ' ' . _x( 'until', 'Time period end date', $this->plugin_slug ) . ' ' . $calendR_objs['end']->format( 'd F Y' ) ;
        $text_dates = apply_filters( '', $text_dates, $calendR_objs, $deal );

        return do_shortcode(
            '[vc_column_text]<h3>' . esc_html( $title ) . '</h3>[/vc_column_text]'
            . '[vc_separator type="transparent" up="10" down="10"]'
            . '[qode_elements_holder number_of_columns="two_columns"]'
            . '[qode_elements_holder_item item_padding="0px 20px 0px 0px" vertical_alignment="middle" advanced_animations="no"]'
            . '[product id="' . esc_attr( $deal->get( 'product_id' ) ) . '"][/qode_elements_holder_item]'
            . '[qode_elements_holder_item vertical_alignment="top" advanced_animations="no"]'
            . '[icon_text box_type="normal" icon_pack="linea_icons" linea_icon="icon-basic-calendar" icon_type="normal" icon_position="left" icon_size="fa-5x" use_custom_icon_size="no" title="'.__( 'Week' , $this->plugin_slug ).' #' . esc_html( $deal->get( 'week_num' ) ) . '" title_tag="h4" separator="no" '
            . 'text="' . esc_html( $text_dates ) . '" icon_color="#a5dee9" icon_hover_color="#72c1d0" title_color="#fafafa" text_color="#cccccc"]'
            . '[social_share_list][/qode_elements_holder_item][/qode_elements_holder]'
        );
    }

    /**
     * Get a blank deal's HTML output.
     *
     * @since    1.0.0
     *
     * @param   int     $week_num  (Optional) The deal's week number. Defaults to
     *                             the current week's week number.
     *
     * @return  string             HTML output for the blank deal.
     */
    protected function get_blank_deal_html( $week_num=0 )
    {
        $week_num = $week_num ? $week_num : date( 'W' );

        if ( $week_num < MC_Dotw::YEAR_IN_WEEKS ) {
            $msg_is_blank   = '<div class="blank_deal">'
                . '<h3>'
                . _x( 'Coming Soon!', 'Deal of the week: Blank Title', $this->plugin_slug )
                . ' ' . __( 'Discover our next hot deal of the week', $this->plugin_slug )
                . '</h3>'
                . '</div>';

            $next_deal_atts = array( 'week_num' => $week_num + 1 );

            return $msg_is_blank . $this->shortcode( $next_deal_atts );

        } else {
            $msg_blank_endOfYear = '<div class="blank_deal">'
                . '<h3>'
                . __( 'No more deals of the week scheduled for this year (…so far)! ', $this->plugin_slug )
                . '</h3>'
                . '</div>';

            return $msg_blank_endOfYear;
        }
    }

    /**
     * Display the deal of the week using textInline layout.
     *
     * @since    1.0.0
     *
     * @param   array   $atts  Shortcode attributes.
     *
     * @return  string         Returns/echoes out the HTML output displaying the deal of the week.
     */
    public function shortcode_inline( $atts=array() )
    {
        if ( ! current_user_can( 'manage_post' ) ) {
            return;
        }

        $atts = shortcode_atts( array(
            'skin'     => ''
            ), $atts );

        return '<p>Just testing…</p>';
    }
}
