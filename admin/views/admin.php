<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
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

do_action( 'dotw_before_admin_page_display' );

$plugin_slug = $this->plugin_slug;
?>

<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><?php esc_attr_e( 'MC | Deal Of The Week', $plugin_slug ); ?></h1>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', $plugin_slug ); ?>"><br></div>
						<!-- Toggle -->

						<h2 class="hndle"><span><?php esc_attr_e( 'Edition Panels: Manage deals', $plugin_slug ); ?> | <?php esc_html_e( date('Y') ); ?></span>
						</h2>

						<div class="inside">
							<form name="dotw_deals_form" id="dotw_deals_form" method="post" action="">
								<input type="hidden" name="dotw_deals_form_submitted" value="Y">
								<div id="accordion">
									<?php
									$options = $this->get_options();

									$nav_links_html = '';
									$endofweek_offset = $options['settings']['endofweek_offset'];

									// Looping over each week/deal of the year.
									for ($i=0; $i < MC_Dotw::YEAR_IN_WEEKS; $i++) {
										$week_num        = $i+1;
										$deal            = MC_Dotw_Deal::new_deal( $week_num );

										// Get week date ranges (@see: nav hover titles)
										// + get appropriate date fields content
										$week_clndr      = $deal->get_calendar_objects();
										$displayed_dates = $deal->get_optionsform_datefields_val();

										// Store this long strings, just to make the below HTML markup more reader-friendly.
										$fromID 		 = "dotw_deals_" . $week_num . "_date_from";
										$toID   		 = "dotw_deals_" . $week_num . "_date_to";

										// Add a specific CSS class to blank/active deals.
										$title_class  = $deal->is_blank()    ? 'muted'        : '';
										$title_class .= $deal->is_active()   ? ' dotw-active' : '';


										$sale_placeholder = $deal->get( 'sale_price' )
											? $deal->get( 'sale_price' )
											: $deal->get( 'hot_price' );

										// Prepare and agregate navigation links while in the loop.
										$nav_link_css = $deal->is_blank() ? 'muted' : '';

										if ( $deal->is_current() ) {
											$nav_link_css = 'dotw-nav-current';

											if ( $deal->is_blank() ) {
												$nav_link_css .= ' dotw-nav-blank';
											} elseif ( ! $deal->is_active() ) {
												$nav_link_css .= ' dotw-nav-inactive';
											}
										}

										$nav_links_html .= '<a '
											. 'href="#dotw_deals_' . esc_attr( $week_num-1 ) . '_title" '
											. 'class="' . esc_attr( $nav_link_css ) . '" '
											. 'title="'.__( 'Jump to', $plugin_slug ).': ' . esc_attr( $week_clndr['start']->format( 'd M' ) ). ' - ' . esc_attr( $week_clndr['end']->format( 'd M' ) ) . '">'
											. 	esc_html( $week_num )
											. '</a>';

										$nav_links_html .= ($i === (MC_Dotw::YEAR_IN_WEEKS - 1)) ? '' : ' - ';

										//Looped item HTML content template: ?>
										<div>
											<h3>
												<a href="#" id="dotw_deals_<?php esc_attr_e($week_num);?>_title" class="<?php esc_attr_e( $title_class ); ?>" name="dotw_deals_<?php esc_attr_e($week_num);?>_title">
													<?php echo __( 'Week', $plugin_slug ) . ' #' . $week_num . ' | ' . $week_clndr['start']->format( 'd M' ) . ' ' . _x('to', 'Time period end date', $plugin_slug) . ' ' . $week_clndr['end']->format( 'd M' ) . ' | <span class="cur_selection">'.$deal->get( 'title' ).'</span>'; ?>
												</a>
											</h3>
											<div><?php //Accordion innerDiv ?>
												<table class="form-table">
													<tr valign="top">
														<td>
															<p class="dotw_deals_option_input">
																<input type="text" list="dotw_deals_suggest_list" value="<?php esc_attr_e( $deal->get( 'title' ) );?>" class="regular-text dotw_deals_suggest" id="dotw_deals_<?php esc_attr_e($week_num);?>" name="dotw_deals_<?php esc_attr_e($week_num);?>" placeholder="<?php esc_attr_e( 'Deal: enter an instrument\'s name…', $plugin_slug );?>"/>
															</p>
															<p class="dotw_deals_option_details">
																<input type="number" min="0" max="99999" value="<?php esc_html_e( $deal->get( 'hot_price' ) );?>" class="small" name="dotw_deals_<?php esc_attr_e($week_num);?>_hot_price" id="dotw_deals_<?php esc_attr_e($week_num);?>_hot_price" placeholder="<?php esc_html_e( $deal->get( 'sale_price' ) );?>"/><span class="description"> <?php esc_attr_e( 'Hot Deal\'s Price', $plugin_slug ); ?></span><br>
															</p>
															<div id="dotw_deals_<?php esc_attr_e($week_num);?>_aside">
																<?php echo $this->get_deal_aside_html( $deal ); ?>
															</div>
														</td>
														<td scope="row">
															<?php
															?>
															<p class="dotw_wc_data dotw_prices">
																<input type="number" min="0" max="99999" value="<?php esc_html_e( $deal->get_product_meta()['_regular_price'][0] );?>" class="small" name="dotw_deals_<?php esc_attr_e($week_num);?>_regular_price" id="dotw_deals_<?php esc_attr_e($week_num);?>_regular_price"/><span class="description"> <?php esc_attr_e( 'Regular Price', $plugin_slug ); ?></span><br>
																<input type="number" min="0" max="99999" value="<?php esc_html_e( $deal->get_product_meta()['_sale_price'][0] );?>" class="small" name="dotw_deals_<?php esc_attr_e($week_num);?>_sale_price" id="dotw_deals_<?php esc_attr_e($week_num);?>_sale_price" placeholder="<?php esc_html_e( $sale_placeholder );?>"/><span class="description"> <?php esc_attr_e( 'Sale Price', $plugin_slug ); ?></span></p>
															<p class="dotw_wc_data dotw_period">
																<input type="text" class="datepicker_from" name="<?php esc_attr_e($fromID); ?>" id="<?php esc_attr_e($fromID); ?>" placeholder="<?php esc_attr_e( 'From', $plugin_slug );?>… YYYY-MM-DD" value="<?php echo $displayed_dates['date_from'];?>"><span class="description"> <?php esc_attr_e( 'Date from', $plugin_slug ); ?></span><br>
																<input type="text" class="datepicker_to" name="<?php esc_attr_e($toID); ?>" id="<?php esc_attr_e($toID); ?>" placeholder="<?php esc_attr_e( 'To', $plugin_slug );?>… YYYY-MM-DD" value="<?php echo $displayed_dates['date_to'];?>"><span class="description"> <?php esc_attr_e( 'Date to', $plugin_slug ); ?></span></p>
																<input type="hidden" name="dotw_deals_<?php esc_attr_e($week_num); ?>_wc_data_edited" id="dotw_deals_<?php esc_attr_e($week_num); ?>_wc_data_edited" value="">
																<input type="hidden" name="dotw_deals_<?php esc_attr_e($week_num); ?>_product_id" id="dotw_deals_<?php esc_attr_e($week_num); ?>_product_id" value="<?php esc_attr_e( $deal->get( 'product_id' ) );?>">
														</td>
													</tr>
												</table>
											</div><?php //End accordion innerDiv ?>
										</div><?php //End looped item HTML content template ?>
									<?php } 	//End loop for $i < MC_Dotw::YEAR_IN_WEEKS ?>
								</div><?php //End accordion outer div ?>
								<input class="button-primary" type="submit" name="dotw_deals_form_submit" value="<?php esc_attr_e( 'Save changes', $plugin_slug ); ?>" />
							</form>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">

						<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', $plugin_slug ); ?>"><br></div>
						<!-- Toggle -->

						<h2 class="hndle"><span><?php esc_html_e(
									'Navigation & Global Settings', $plugin_slug
								); ?></span></h2>

						<div class="inside">
							<div id="accordion-side">
								<div>
									<h3><a href="#"><?php esc_html_e( 'Weeks', $plugin_slug ); ?>: <?php esc_html_e( 'Quick Nav', $plugin_slug ); ?></a></h3>
									<div><?php echo $nav_links_html; ?></div>
								</div>
								<div>
									<h3><a href="#"><?php esc_html_e( 'End Of The Deal', $plugin_slug ); ?></a></h3>
									<div>
										<p><span class="description"> <?php esc_attr_e( 'The day of the week on which all deals should end.', $plugin_slug ); ?></span></p>
										<select name="dotw_deals_endofweek_offset" id="dotw_deals_endofweek_offset" form="dotw_deals_form">
											<option value="4" <?php selected( '4', $endofweek_offset ); ?>><?php _e( 'Wednesday', $plugin_slug )?></option>
											<option value="3" <?php selected( '3', $endofweek_offset ); ?>><?php _e( 'Thursday', $plugin_slug )?></option>
											<option value="2" <?php selected( '2', $endofweek_offset ); ?>><?php _e( 'Friday', $plugin_slug )?></option>
											<option value="1" <?php selected( '1', $endofweek_offset ); ?>><?php _e( 'Saturday', $plugin_slug )?></option>
											<option value="0" <?php selected( '0', $endofweek_offset ); ?>><?php _e( 'Sunday', $plugin_slug )?></option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables -->

			</div>
			<!-- #postbox-container-1 .postbox-container -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
