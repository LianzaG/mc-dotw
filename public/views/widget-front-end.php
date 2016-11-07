<?php
/**
 * MC_Dotw widget.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 * @see       /admin/includes/class-mc-dotw-admin-widget.php : widget().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo $before_widget;

echo $before_title . $instance['title'] . $after_title;

// Output formats.
if ( 'default' === $instance['format'] ) :

	echo do_shortcode( '[product id="' . $deal->get( 'product_id' ) . '"]' );

elseif ( 'slick' === $instance['format'] ) :
	?>
	<div class="dotw-w-wrapper dotw-w-slick">
		<div class="dotw-w-slicklist" data-slick='{<?php echo $data_attr; ?>}'>
			<?php
			foreach ( $deals as $key => $deal ) :
				// ----------------------------------
				// Build the 'timing' content string.
				// ----------------------------------
				$timing = _x( 'From ', 'Time period start date' ,$this->plugin_slug )
		            . ' ' . $deal->get( 'start' )->format( 'd' )
		            . ' ' . _x( 'until', 'Time period end date', $this->plugin_slug )
		            . ' ' . $deal->get( 'end' )->format( 'd F' );

		        $deal->clear_calendar();	// Lighter memory footprint.

		        $timing = $deal->is_current()
		            ? _x( 'This week only!', 'Widget Slick caption', $this->plugin_slug )
		            : $timing;

		        // ---------------------------------------------------------------------------
				// HTML output (Caption top/bottom position depends on the instance settings).
				// ---------------------------------------------------------------------------
		        ?>
				<div>
					<?php ob_start(); ?>

					<p class="dotw-slick-caption dotw-slick-pricing">
						<span class="dotw-hot-price"><?php echo esc_html( $deal->get( 'price' ) ); ?> €</span> <?php echo _x( 'instead of', 'Sale price', $this->plugin_slug ); ?>
						<span class="dotw-reg-price"><?php echo esc_html( $deal->get( 'regular_price' ) ); ?> €</span>
					</p>

					<?php $caption_ob_price = ob_get_clean(); ?>

					<?php ob_start(); ?>

					<p class="dotw-slick-caption dotw-slick-timing"><?php echo esc_html( $timing ); ?></p>

					<?php $caption_ob_time = ob_get_clean(); ?>

					<?php if ( 'top' === $instance['pos_timing'] && 'yes' === $instance['flip_caption'] ) echo $caption_ob_time; ?>
					<?php if ( 'top' === $instance['pos_pricing'] ) echo $caption_ob_price; ?>
					<?php if ( 'top' === $instance['pos_timing'] && '' === $instance['flip_caption'] ) echo $caption_ob_time; ?>

					<?php echo do_shortcode( '[product id="' . $deal->get( 'product_id' ) . '" class="dotw-wide-li"]' ); ?>
					<div class="dotw-slick-caption-adjust"></div>

					<?php if ( '' === $instance['pos_timing'] && 'yes' === $instance['flip_caption'] ) echo $caption_ob_time; ?>
					<?php if ( '' === $instance['pos_pricing'] ) echo $caption_ob_price; ?>
					<?php if ( '' === $instance['pos_timing'] && '' === $instance['flip_caption'] ) echo $caption_ob_time; ?>
				</div>
			<?php
			endforeach; // Deal.
			?>
		</div>
	</div>
<?php
endif; // Output formats.

echo $after_widget;
