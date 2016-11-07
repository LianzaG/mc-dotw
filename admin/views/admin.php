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

$options           = $this->get_options();
$plugin_slug       = $this->plugin_slug;
$form_name         = "mc_dotw_admin_form";
$slick             = $options['settings']['widget']['slick'];
$slick_prefix_name = 'dotw_deals_widget_slick';
$slick_prefix_id   = 'dotw_deals_widget_slick_';
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
							<form name="<?php echo esc_attr( $form_name ); ?>" id="<?php echo esc_attr( $form_name ); ?>" method="post" action="">
								<input type="hidden" name="dotw_deals_form_submitted" value="Y">
								<div id="accordion">
									<?php
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

						<h2 class="hndle"><span><?php esc_html_e( 'Navigation', $plugin_slug ); ?></span></h2>

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
										<select name="dotw_deals_endofweek_offset" id="dotw_deals_endofweek_offset" form="<?php echo esc_attr( $form_name ); ?>">
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

				<div class="meta-box-sortables">

					<div class="postbox">

						<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', $plugin_slug ); ?>"><br></div>
						<!-- Toggle -->

						<h2 class="hndle"><span><?php esc_html_e( 'Global Settings', $plugin_slug ); ?></span></h2>

						<div class="inside">
							<div id="accordion-side">
								<div>
									<h3><a href="#"><?php esc_html_e( 'Widget - Default parameters', $plugin_slug ); ?></a></h3>
									<div class="widget-dotw_widget-slick-wrapper">
										<p><span class="description"> <?php esc_attr_e( 'These settings will be applied by default if you don\'t specify new values when creating a new widget using the "Slick Slider" layout format.', $plugin_slug ); ?></span></p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[dots]' ); ?>" title="<?php _e( 'Show dot indicators', $this->plugin_slug ); ?>"><?php _e( 'Dots', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[dots]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'dots' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['dots'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['dots'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[dotsClass]' ); ?>" title="<?php _e( 'Class for slide indicator dots container.', $this->plugin_slug ); ?>"><?php _e( 'Dots CSS Class', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'dotsClass' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[dotsClass]' ); ?>" value="<?php echo esc_attr( $slick['dotsClass'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[draggable]' ); ?>" title="<?php _e( 'Enable mouse dragging.', $this->plugin_slug ); ?>"><?php _e( 'Draggable', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[draggable]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'draggable' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['draggable'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['draggable'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[fade]' ); ?>" title="<?php _e( 'Enable fade.', $this->plugin_slug ); ?>"><?php _e( 'Fade', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[fade]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'fade' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['fade'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['fade'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[infinite]' ); ?>" title="<?php _e( 'Infinite loop sliding.', $this->plugin_slug ); ?>"><?php _e( 'Infinite', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[infinite]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'infinite' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['infinite'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['infinite'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[initialSlide]' ); ?>" title="<?php _e( 'Slide to start on.', $this->plugin_slug ); ?>"><?php _e( 'Initial Slide', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'initialSlide' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[initialSlide]' ); ?>" value="<?php echo esc_attr( $slick['initialSlide'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[speed]' ); ?>" title="<?php _e( 'Slide/Fade animation speed.', $this->plugin_slug ); ?>"><?php _e( 'Speed', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'speed' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[speed]' ); ?>" value="<?php echo esc_attr( $slick['speed'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[slidesToShow]' ); ?>" title="<?php _e( '# of slides to show.', $this->plugin_slug ); ?>"><?php _e( 'Slides To Show', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'slidesToShow' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[slidesToShow]' ); ?>" value="<?php echo esc_attr( $slick['slidesToShow'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[slidesToScroll]' ); ?>" title="<?php _e( '# of slides to scroll', $this->plugin_slug ); ?>"><?php _e( 'Slides To Scroll', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'slidesToScroll' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[slidesToScroll]' ); ?>" value="<?php echo esc_attr( $slick['slidesToScroll'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[centerMode]' ); ?>" title="<?php _e( 'Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.', $this->plugin_slug ); ?>"><?php _e( 'Center Mode', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[centerMode]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'centerMode' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['centerMode'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['centerMode'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[centerPadding]' ); ?>" title="<?php _e( 'Side padding when in center mode (px or %).', $this->plugin_slug ); ?>"><?php _e( 'Center Padding', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'centerPadding' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[centerPadding]' ); ?>" value="<?php echo esc_attr( $slick['centerPadding'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[autoplay]' ); ?>" title="<?php _e( 'Enables Autoplay.', $this->plugin_slug ); ?>"><?php _e( 'Autoplay', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[autoplay]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'autoplay' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['autoplay'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['autoplay'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[autoplaySpeed]' ); ?>" title="<?php _e( 'Autoplay Speed in milliseconds.', $this->plugin_slug ); ?>"><?php _e( 'Autoplay Speed', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'autoplaySpeed' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[autoplaySpeed]' ); ?>" value="<?php echo esc_attr( $slick['autoplaySpeed'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[arrows]' ); ?>" title="<?php _e( 'Prev/Next Arrows', $this->plugin_slug ); ?>"><?php _e( 'Arrows', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[arrows]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'arrows' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['arrows'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['arrows'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[variableWidth]' ); ?>" title="<?php _e( 'Variable width slides.', $this->plugin_slug ); ?>"><?php _e( 'Varialble Width', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[variableWidth]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'variableWidth' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['variableWidth'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['variableWidth'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[lazyload]' ); ?>" title="<?php _e( 'Lazy loading technique: \'ondemand\' or \'progressive\'.', $this->plugin_slug ); ?>"><?php _e( 'Lazyload', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[lazyload]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'lazyload' ); ?>" class="widefat">
												<option value="ondemand" <?php selected( 'ondemand', $slick['lazyload'] ); ?>><?php _e( 'On Demand', $this->plugin_slug ); ?></option>
												<option value="progressive" <?php selected( 'progressive', $slick['lazyload'] ); ?>><?php _e( 'Progressive', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[vertical]' ); ?>" title="<?php _e( 'Vertical slide mode.', $this->plugin_slug ); ?>"><?php _e( 'Vertical', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[vertical]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'vertical' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['vertical'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['vertical'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[verticalSwiping]' ); ?>" title="<?php _e( 'Vertical swipe mode.', $this->plugin_slug ); ?>"><?php _e( 'Vertical Swiping', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[verticalSwiping]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'verticalSwiping' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['verticalSwiping'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['verticalSwiping'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[zIndex]' ); ?>" title="<?php _e( 'Set the zIndex values for slides, useful for IE9 and lower.', $this->plugin_slug ); ?>"><?php _e( 'zIndex', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'zIndex' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[zIndex]' ); ?>" value="<?php echo esc_attr( $slick['zIndex'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[adaptiveHeight]' ); ?>" title="<?php _e( 'Enables adaptive height for single slide horizontal carousels.', $this->plugin_slug ); ?>"><?php _e( 'Adaptive Height', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[adaptiveHeight]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'adaptiveHeight' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['adaptiveHeight'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['adaptiveHeight'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[pauseOnHover]' ); ?>" title="<?php _e( 'Pause Autoplay On Hover.', $this->plugin_slug ); ?>"><?php _e( 'Pause On Hover', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[pauseOnHover]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'pauseOnHover' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['pauseOnHover'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['pauseOnHover'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[pauseOnDotsHover]' ); ?>" title="<?php _e( 'Pause Autoplay when a dot is hovered.', $this->plugin_slug ); ?>"><?php _e( 'Pause On Dots Hover', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[pauseOnDotsHover]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'pauseOnDotsHover' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['pauseOnDotsHover'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['pauseOnDotsHover'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[respondTo]' ); ?>" title="<?php _e( 'Width that responsive object responds to. Can be \'window\', \'slider\' or \'min\' (the smaller of the two).', $this->plugin_slug ); ?>"><?php _e( 'Respond To', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[respondTo]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'respondTo' ); ?>" class="widefat">
												<option value="window" <?php selected( 'window', $slick['respondTo'] ); ?>><?php _e( 'Window', $this->plugin_slug ); ?></option>
												<option value="slider" <?php selected( 'slider', $slick['respondTo'] ); ?>><?php _e( 'Slider', $this->plugin_slug ); ?></option>
												<option value="min" <?php selected( 'min', $slick['respondTo'] ); ?>><?php _e( 'Min', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[rows]' ); ?>" title="<?php _e( 'Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.', $this->plugin_slug ); ?>"><?php _e( 'Rows', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'rows' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[rows]' ); ?>" value="<?php echo esc_attr( $slick['rows'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[slidesPerRow]' ); ?>" title="<?php _e( 'With grid mode intialized via the rows option, this sets how many slides are in each grid row.', $this->plugin_slug ); ?>"><?php _e( 'Slides Per Row', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'slidesPerRow' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[slidesPerRow]' ); ?>" value="<?php echo esc_attr( $slick['slidesPerRow'] ); ?>"/>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[useCss]' ); ?>" title="<?php _e( 'Enable/Disable CSS Transitions.', $this->plugin_slug ); ?>"><?php _e( 'Use Css', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[useCss]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'useCss' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['useCss'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['useCss'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[useTransform]' ); ?>" title="<?php _e( 'Enable/Disable CSS Transforms.', $this->plugin_slug ); ?>"><?php _e( 'Use Transform', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[useTransform]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'useTransform' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['useTransform'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['useTransform'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[rtl]' ); ?>" title="<?php _e( 'Change the slider\'s direction to become right-to-left.', $this->plugin_slug ); ?>"><?php _e( 'Right-to-left', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[rtl]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'rtl' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['rtl'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['rtl'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[waitForAnimate]' ); ?>" title="<?php _e( 'Ignores requests to advance the slide while animating.', $this->plugin_slug ); ?>"><?php _e( 'Wait For Animate', $this->plugin_slug ); ?></label>
											<select form="<?php echo esc_attr( $form_name ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[waitForAnimate]' ); ?>" id="<?php echo esc_attr( $slick_prefix_id . 'waitForAnimate' ); ?>" class="widefat">
												<option value="true" <?php selected( 'true', $slick['waitForAnimate'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
												<option value="false" <?php selected( 'false', $slick['waitForAnimate'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
											</select>
										</p>
										<p>
											<label for="<?php echo esc_attr( $slick_prefix_name . '[touchThreshold]' ); ?>" title="<?php _e( 'To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.', $this->plugin_slug ); ?>"><?php _e( 'Touch Threshold', $this->plugin_slug ); ?></label>
											<input form="<?php echo esc_attr( $form_name ); ?>" type="text" class="widefat" id="<?php echo esc_attr( $slick_prefix_id . 'touchThreshold' ); ?>" name="<?php echo esc_attr( $slick_prefix_name . '[touchThreshold]' ); ?>" value="<?php echo esc_attr( $slick['touchThreshold'] ); ?>"/>
										</p>
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
