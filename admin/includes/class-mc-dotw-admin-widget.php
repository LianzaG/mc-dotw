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
        // Instantiate the parent object
        parent::__construct( false, 'MC Deal Of The Week - Widget' );
        $this->plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();
    }

    /**
     * Render the widget.
     *
     * @since   1.0.0
     */
    function widget( $args, $instance )
    {
        $options = MC_Dotw_Admin::get_instance()->get_options();

        // Widget output
        extract( $args );
        $title  = apply_filters( 'dotw_widget_title', $instance['title'] );

        require( plugins_url( $this->plugin_slug . '/public/views/widget-front-end.php' ) );
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
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    /**
     * Get widget's form/form fields.
     *
     * @since   1.0.0
     */
    function form( $instance )
    {
        // Output admin widget options
        $title = isset( $instance['title'] )
            ? esc_attr( $instance['title'] )
            : '';

        require( '../views/widget-fields.php' );
    }
}

/**
 * Register the MC_Dotw_Widget Class.
 *
 * @since   1.0.0
 *
 * @param   void
 *
 * @return  void
 */
if ( ! function_exists( 'mc_dotw_register_widgets') ) {
    function mc_dotw_register_widgets()
    {
        register_widget( 'MC_Dotw_Widget' );
    }
}
add_action( 'widgets_init', 'mc_dotw_register_widgets' );
