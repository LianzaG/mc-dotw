<?php

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = $deal->get_the_product();
$plugin_slug = MC_Dotw::get_instance()->get_plugin_slug();

// If the deal is active or shares the product it promotes with one or more other deals.
if ( $deal->has_activity() || ( $product && count( $product->get_deals() ) > 1 ) ) : ?>
	<?php if ( $product && count( $product->get_deals() ) > 1 ) :
		// Get all product deals, get potential week_num of any active deal other than $deal
		// and associated to the product and, finally, get a filtered list of deals that does not
		// include the looped $deal nor the potential other active deal. Then, display if needed.
		$product_deals          = $product->get_deals();
		$elsewhere_active_num   = $deal->has_product_active_elsewhere() ? $product->get_active_deal()['week_num'] : 0;
		$nav_anchors            = array();

		ksort( $product_deals, SORT_NUMERIC );

		foreach ( $product_deals as $deal_num => $deal_dataset ) {
			$css = '';
			$activity_text = '';

			if ( $deal_num == $elsewhere_active_num || ( $deal->is_active() && $deal_num == $deal->get( 'week_num' ) ) ) {
				$css = ' class="dotw-nav-current"';
				// $activity_text = ' (' . _x( 'Active', 'Deal Aside Wrapper: Deals list', $plugin_slug ) . ')';
			}

			$nav_anchors[] = '<a href="#dotw_deals_' . ($deal_num-1) . '_title"' . $css . '>' . $deal_num . $activity_text . '</a>';
		}
		?>

		<?php if ( count( $nav_anchors ) ) : ?>
			<p id="dotw_deal_<?php esc_html_e( $week_num ); ?>_related_deals"> <?php
				echo esc_html( _n( 'Related deal:', 'Related deals:', count( $nav_anchors ), $plugin_slug ) );
		     	echo ' ' . implode( ', ', $nav_anchors );
				?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $deal->has_activity() && ! $deal->is_blank() ):
		$sale_price_text = $deal->get( 'sale_price' )
			? __( 'Usual sale price: ', $plugin_slug ) . $deal->get( 'sale_price' ) . ' ' . get_woocommerce_currency_symbol()
			: _x( 'None', 'No price', $plugin_slug );

		$date_from_text  = $deal->get( 'date_from' )
			? __( 'Usual sale start date: ', $plugin_slug ) . $deal->get( 'date_from' )
			: _x( 'None', 'No date', $plugin_slug );

		$date_to_text    = $deal->get( 'date_to' )
			? __( 'Usual sale end date: ', $plugin_slug ) . $deal->get( 'date_to' )
			: _x( 'None', 'No date', $plugin_slug );
	?>

		<p id="dotw_deal_<?php esc_html_e( $week_num ); ?>_usual_sale_price"> <?php echo esc_html( $sale_price_text ); ?> </p>
		<p id="dotw_deal_<?php esc_html_e( $week_num ); ?>_usual_sale_date_from"> <?php echo esc_html( $date_from_text ); ?> </p>
		<p id="dotw_deal_<?php esc_html_e( $week_num ); ?>_usual_sale_date_to"> <?php echo esc_html( $date_to_text ); ?> </p>
	<?php endif; ?>

<?php endif;
