<?php
/**
 * The MC_Dotw_Widget Class.
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

class MC_Dotw_Widget extends WP_Widget
{
    /**
     * Constructor.
     *
     * @since   1.0.0
     */
    function __construct()
    {
        $this->plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();

        // Instantiate the parent object
        parent::__construct(
            'dotw_widget',                  // Base ID
            'Deal Of The Week - Widget',    // Name
            array(
                'description' => __( 'Get the current deal of the week.', $this->plugin_slug ),
            ) // Args
        );
    }

    /**
     * Render the widget.
     *
     * @since   1.0.0
     */
    function widget( $args, $instance )
    {
        // Parse the settings attibutes supplied to the widget instance.
        $this->parse_instance_atts( $instance );

        // Widget output wrappers ($before_widget, $before_title, $after_title & $after_widget).
        extract( $args );

        // Allow filtering the widget's 'title' (+ also sets a proxy $var to $instance['title']).
        $title = apply_filters( 'dotw_widget_title', $instance['title'] );

        // Get our deal(s) object(s) proxies.
        $deal  = new MC_Dotw_Deal();

        $deals = 'default' !== $instance['format']
            ? MC_Dotw::get_instance()->get_set_deals(
                '',                                              // $key arg.
                $instance['period'],                             // $cardinality arg.
                ! empty( $instance['show_current'] ),            // $current arg.
                (int) $instance['limit'],                        // $limit arg.
                array(
                    'week_num_min' => $instance['week_num_min'], // $range[] arg.
                    'week_num_max' => $instance['week_num_max']  // $range[] arg.
                )
              )
            : array();

        // If there is content to show, be it a unique deal (default format) or a list of deals:
        if ( ! $deal->is_blank() || ! empty( $deals ) ) {
            // When using the 'slick' format.
            if ( 'slick' === $instance['format'] ) {
                // Set container div's 'data-slick' attribute's value.
                $data_attr = $this->get_slick_data_attr( $instance );
            }

            // Display.
            include( dirname( dirname( dirname(__FILE__) ) ) . '/public/views/widget-front-end.php' );
        }
    }

    /**
     * Update.
     *
     * @since   1.0.0
     *
     * @return  MC_Dotw_Widget    The updated widget instance.
     */
    function update( $new_instance, $old_instance )
    {
        // Save widget options
        $instance = array();

        foreach ($new_instance as $key => $val) {
            $key = esc_attr( $key );
            $val = esc_html( $val );

            $instance[$key] = $val;
        }

        return $instance;
    }

    /**
     * Get widget's form/form fields.
     *
     * @since   1.0.0
     */
    function form( $instance )
    {
        $this->parse_instance_atts( $instance );

        // Output admin widget options
        $title = isset( $instance['title'] )
            ? esc_attr( $instance['title'] )
            : __( 'New title', $this->plugin_slug );

        include( dirname( dirname(__FILE__) ) . '/views/widget-fields.php' );
    }

    /**
     * Parse the settings attibutes supplied to the widget instance. Merge with default valuess.
     *
     * The instance argument is passed by reference.
     *
     * @since   1.0.2
     *
     * @param   array  &$instance  (Required) Associative array. The widget instance's settings.
     *
     * @return  void               Argument passed by reference.
     */
    protected function parse_instance_atts( &$instance )
    {
        $instance = wp_parse_args( $instance, self::get_slick_defaults() );
    }

    /**
     * Init slick.js layout's default plugin options.
     *
     * Triggered on pluin activation.
     *
     * @since   1.0.2
     *
     * @return  array  Associative array of widget layout settings default options.
     */
    public static function get_slick_init_defaults()
    {
        return array(
            'title'             => '',              // Widget title.
            'format'            => 'default',       // The HTML output template: 'default' or 'slick'.
            'limit'             => '0',             // Max. number of deals to get. '0' for 'all'.
            'period'            => 'future',        // Enable time-filtering. Accepts 'past', 'future' or '' (none).
            'show_current'      => 'yes',           // Should the current week's deal be included?
            'pos_timing'        => '',              // Use 'top' to place the dates above the product image rather than below.
            'pos_pricing'       => '',              // Use 'top' to place the prices above the product image.
            'flip_caption'      => '',              // 'yes' to place prices above the date (when both are placed on top/bottom).
            'week_num_min'      => '',              // Minimum deal weel number. Deals before this week number won't be returned.
            'week_num_max'      => '',              // Maximum deal weel number. Deals after this week number won't be returned.
                                                    // -- Slick settings.
            'dots'              => 'true',          // Show dot indicators
            'dotsClass'         => 'slick-dots dotw-dots',    // Class for slide indicator dots container.
            'draggable'         => 'true',          // Enable mouse dragging.
            'fade'              => 'false',         // Enable fade.
            'infinite'          => 'true' ,         // Infinite loop sliding.
            'initialSlide'      => '0',             // Slide to start on.
            'speed'             => '400',           // Slide/Fade animation speed.
            'slidesToShow'      => '1',             // # of slides to show.
            'slidesToScroll'    => '1',             // # of slides to scroll
            'centerMode'        => 'false' ,        // Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.
            'centerPadding'     => '50px',          // Side padding when in center mode (px or %).
            'autoplay'          => 'true' ,         // Enables Autoplay.
            'autoplaySpeed'     => '6000',          // Autoplay Speed in milliseconds.
            'arrows'            => 'true',          // Prev/Next Arrows
            'variableWidth'     => 'false',         // Variable width slides.
            'lazyload'          => 'ondemand',      // Lazy loading technique: 'ondemand' or 'progressive'.
            'vertical'          => 'false',         // Vertical slide mode.
            'verticalSwiping'   => 'false',         // Vertical swipe mode.
            'zIndex'            => '1000',          // Set the zIndex values for slides, useful for IE9 and lower.
            'adaptiveHeight'    => 'false',         // Enables adaptive height for single slide horizontal carousels.
            'pauseOnHover'      => 'true',          // Pause Autoplay On Hover.
            'pauseOnDotsHover'  => 'false',         // Pause Autoplay when a dot is hovered.
            'respondTo'         => 'window',        // Width that responsive object responds to. Can be 'window', 'slider' or 'min' (the smaller of the two).
            'rows'              => '1',             // Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.
            'slidesPerRow'      => '1',             // With grid mode intialized via the rows option, this sets how many slides are in each grid row.
            'useCss'            => 'true',          // Enable/Disable CSS Transitions.
            'useTransform'      => 'true',          // Enable/Disable CSS Transforms.
            'rtl'               => 'false',         // Change the slider's direction to become right-to-left.
            'waitForAnimate'    => 'true',          // Ignores requests to advance the slide while animating.
            'touchThreshold'    => '5',             // To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.
        );
    }

    /**
     * Get slick.js layout's user-defined default options.
     *
     * @since   1.0.2
     *
     * @return  array  Associative array of user-defined layout settings default options.
     */
    public static function get_slick_defaults()
    {
        $options = MC_Dotw_Admin::get_instance()->get_options();
        $slick   = $options['settings']['widget']['slick'];

        return array(
            'title'             => '',
            'format'            => 'default',
            'limit'             => '0',
            'period'            => 'future',
            'show_current'      => 'yes',
            'pos_timing'        => '',
            'pos_pricing'       => '',
            'flip_caption'      => '',
            'week_num_min'      => '',
            'week_num_max'      => '',

            'dots'              => $slick['dots'],
            'dotsClass'         => $slick['dotsClass'],
            'draggable'         => $slick['draggable'],
            'fade'              => $slick['fade'],
            'infinite'          => $slick['infinite'] ,
            'initialSlide'      => $slick['initialSlide'],
            'speed'             => $slick['speed'],
            'slidesToShow'      => $slick['slidesToShow'],
            'slidesToScroll'    => $slick['slidesToScroll'],
            'centerMode'        => $slick['centerMode'] ,
            'centerPadding'     => $slick['centerPadding'],
            'autoplay'          => $slick['autoplay'] ,
            'autoplaySpeed'     => $slick['autoplaySpeed'],
            'arrows'            => $slick['arrows'],
            'variableWidth'     => $slick['variableWidth'],
            'lazyload'          => $slick['lazyload'],
            'vertical'          => $slick['vertical'],
            'verticalSwiping'   => $slick['verticalSwiping'],
            'zIndex'            => $slick['zIndex'],
            'adaptiveHeight'    => $slick['adaptiveHeight'],
            'pauseOnHover'      => $slick['pauseOnHover'],
            'pauseOnDotsHover'  => $slick['pauseOnDotsHover'],
            'respondTo'         => $slick['respondTo'],
            'rows'              => $slick['rows'],
            'slidesPerRow'      => $slick['slidesPerRow'],
            'useCss'            => $slick['useCss'],
            'useTransform'      => $slick['useTransform'],
            'rtl'               => $slick['rtl'],
            'waitForAnimate'    => $slick['waitForAnimate'],
            'touchThreshold'    => $slick['touchThreshold'],
        );
    }

    /**
     * Get the value of the widget HTML elements 'data-slick' attribute.
     *
     * Used to set the behaviour of slick.js.
     *
     * @since   1.0.2
     *
     * @param   array  &$instance  (Required) Associative array. The widget instance's settings.
     *
     * @return  string             The 'data-slick' attribute's value.
     */
    protected function get_slick_data_attr( &$instance )
    {
        $data_attr  = '"dots": '                . esc_attr( $instance['dots'] )             . ', ';
        $data_attr .= '"dotsClass": "'          . esc_attr( $instance['dotsClass'] )        . '", '; // String
        $data_attr .= '"draggable": '           . esc_attr( $instance['draggable'] )        . ', ';
        $data_attr .= '"fade": '                . esc_attr( $instance['fade'] )             . ', ';
        $data_attr .= '"infinite": '            . esc_attr( $instance['infinite'] )         . ', ';
        $data_attr .= '"initialSlide": '        . esc_attr( $instance['initialSlide'] )     . ', ';
        $data_attr .= '"speed": '               . esc_attr( $instance['speed'] )            . ', ';
        $data_attr .= '"slidesToShow": '        . esc_attr( $instance['slidesToShow'] )     . ', ';
        $data_attr .= '"slidesToScroll": '      . esc_attr( $instance['slidesToScroll'] )   . ', ';
        $data_attr .= '"centerMode": '          . esc_attr( $instance['centerMode'] )       . ', ';
        $data_attr .= '"centerPadding": "'      . esc_attr( $instance['centerPadding'] )    . '", '; // String
        $data_attr .= '"autoplay": '            . esc_attr( $instance['autoplay'] )         . ', ';
        $data_attr .= '"autoplaySpeed": '       . esc_attr( $instance['autoplaySpeed'] )    . ', ';
        $data_attr .= '"arrows": '              . esc_attr( $instance['arrows'] )           . ', ';
        $data_attr .= '"variableWidth": '       . esc_attr( $instance['variableWidth'] )    . ', ';
        $data_attr .= '"lazyload": "'           . esc_attr( $instance['lazyload'] )         . '", '; // String
        $data_attr .= '"vertical": '            . esc_attr( $instance['vertical'] )         . ', ';
        $data_attr .= '"verticalSwiping": '     . esc_attr( $instance['verticalSwiping'] )  . ', ';
        $data_attr .= '"adaptiveHeight": '      . esc_attr( $instance['adaptiveHeight'] )   . ', ';
        $data_attr .= '"pauseOnHover": '        . esc_attr( $instance['pauseOnHover'] )     . ', ';
        $data_attr .= '"pauseOnDotsHover": '    . esc_attr( $instance['pauseOnDotsHover'] ) . ', ';
        $data_attr .= '"respondTo": "'          . esc_attr( $instance['respondTo'] )       . '", '; // String
        $data_attr .= '"rows": '                . esc_attr( $instance['rows'] )             . ', ';
        $data_attr .= '"slidesPerRow": '        . esc_attr( $instance['slidesPerRow'] )     . ', ';
        $data_attr .= '"useCss": '              . esc_attr( $instance['useCss'] )           . ', ';
        $data_attr .= '"useTransform": '        . esc_attr( $instance['useTransform'] )     . ', ';
        $data_attr .= '"rtl": '                 . esc_attr( $instance['rtl'] )              . ', ';
        $data_attr .= '"waitForAnimate": '      . esc_attr( $instance['waitForAnimate'] )   . ', ';
        $data_attr .= '"touchThreshold": '      . esc_attr( $instance['touchThreshold'] )   . ', ';
        $data_attr .= '"zIndex": '              . esc_attr( $instance['zIndex'] );

        return $data_attr;
    }
}

if ( ! function_exists( 'mc_dotw_register_widgets') ) {
    /**
     * Register the MC_Dotw_Widget Class.
     *
     * @since   1.0.0
     *
     * @param   void
     *
     * @return  void
     */
    function mc_dotw_register_widgets()
    {
        register_widget( 'MC_Dotw_Widget' );
    }
}
add_action( 'widgets_init', 'mc_dotw_register_widgets' );
