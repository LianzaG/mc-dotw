<?php
if(!defined('ABSPATH')) {
	exit;
}

$plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();

?>
<p>
	<label><?php __( 'Title', $plugin_slug ); ?></label>
	<input type="text" class="widefat" name="<?php esc_attr_e( $this->get_field_name('title') ); ?>" value="<?php esc_attr_e($title); ?>"/>
</p>
