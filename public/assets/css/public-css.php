<?php

$slick = $plugin_options['settings']['widget']['slick'];

// header("Content-type: text/css; charset=utf-8");
?>
/* This stylesheet is used to dynamically style the public-facing components of the plugin. */

/* --------------------------------- */
/*             Widget                */
/* --------------------------------- */

/* Don't squeeze image width! */
.columns-4 .woocommerce_with_sidebar div.widget_dotw_widget .dotw-w-slick .dotw-w-slicklist .dotw-wide-li ul.products li.product,
div.widget_dotw_widget .dotw-w-slick .dotw-w-slicklist .dotw-wide-li ul.products li.product,
.dotw-wide-li ul.products li.product {
	width: <?= $slick['css_img_width'] ?>% !important;
}

.slick-slide .woocommerce ul.products {
	margin: 0;
}


/* Widget - Pricing */
.dotw-hot-price {
	color: <?= $slick['css_hot_price_color'] ?>;
}

.dotw-reg-price {
	color: <?= $slick['css_reg_price_color'] ?>;
	text-decoration: <?= $slick['css_reg_price_decoration'] ?>;
}

/* Widget - Caption */
.dotw-slick-caption {
	font-size: <?= $slick['css_caption_font_size'] ?>;
	text-align: <?= $slick['css_caption_align'] ?>;
}

<?php
$pricing_font_size = $slick['css_pricing_font_size'] ? : $slick['css_caption_font_size'];
?>
.dotw-slick-caption.dotw-slick-pricing {
	font-size: <?= $pricing_font_size ?>;
}

.dotw-slick-caption-adjust {
    margin-bottom: -10px;
}

/* Navigation dots */
.dotw-w-slick .slick-dots {
	display: block;
	position: relative;
	bottom: -<?= $slick['css_dots_bottom_dist'] ?>px;
	width:  100%;
	text-align: center;
	border-top: <?= $slick['css_dots_border_top_width'] ?>px <?= $slick['css_dots_border_top_texture'] ?> <?= $slick['css_dots_border_top_color'] ?>;
	border-top: <?= $slick['css_dots_border_top_width'] ?>px <?= $slick['css_dots_border_top_texture'] ?> <?= $slick['css_dots_border_top_color'] ?>;
}

.slick-dots li button::before {
    opacity: <?= $slick['css_dots_inactive_opacity'] ?> !important;
    color: <?= $slick['css_dots_inactive_color'] ?> !important;
}
.slick-dots li.slick-active button:before {
    opacity: <?= $slick['css_dots_active_opacity'] ?> !important;
    color: <?= $slick['css_dots_active_color'] ?> !important;
}
<?php
