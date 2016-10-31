<?php
/**
 * MC_Dotw widget.
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

echo $before_widget;

echo $before_title . $title . $after_title;

echo do_shortcode(
	'[vc_column_text]Le Nord Electro 5D61 à 1599€ au lieu de 1799€[/vc_column_text]'
	. '[vc_separator type="transparent" up="10" down="10"]'
	. '[qode_elements_holder number_of_columns="two_columns"]'
	. '[qode_elements_holder_item item_padding="0px 20px 0px 0px" vertical_alignment="middle" advanced_animations="no"]'
	. '[product id="20294"][/qode_elements_holder_item]'
	. '[qode_elements_holder_item vertical_alignment="top" advanced_animations="no"]'
	. '[icon_text box_type="normal" icon_pack="linea_icons" linea_icon="icon-basic-calendar" icon_type="normal" icon_position="left" icon_size="fa-5x" use_custom_icon_size="no" title="Semaine 41" title_tag="h4" separator="no" text="Du 10 au 16 Octobre 2016" icon_color="#a5dee9" icon_hover_color="#72c1d0" title_color="#fafafa" text_color="#cccccc"]'
	. '[social_share_list][/qode_elements_holder_item][/qode_elements_holder]'
);

echo $after_widget;
