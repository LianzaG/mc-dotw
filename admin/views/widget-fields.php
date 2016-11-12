<?php
/* --------------------- */
/*     Main Settings     */
/* --------------------- */
?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', $this->plugin_slug ); ?></label>
	<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr($title); ?>"/>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php _e( 'Layout:', $this->plugin_slug ); ?></label>
	<select class="widefat"  name="<?php echo esc_attr( $this->get_field_name('format') ); ?>" id="<?php echo esc_attr( $this->get_field_id('format') ); ?>">
		<option value="default" <?php selected( 'default', $instance['format'] ); ?>><?php _e( 'Default', $this->plugin_slug ); ?></option>
		<option value="slick" <?php selected( 'slick', $instance['format'] ); ?>><?php _e( 'Slick Slider', $this->plugin_slug ); ?></option>
	</select>
</p><?php
/* --------------------- */
/*    Slick Settings     */
/* --------------------- */ ?>
<div class="widget-dotw_widget-<?php echo esc_attr( $this->number ); ?>-slick-wrapper"<?php echo 'slick' == $instance['format'] ? '': ' style="display:none;"';?>>
	<hr>
	<div><?php
        /* ---------------------------- */
        /*    Slick - Main Settings     */
        /* ---------------------------- */ ?>
		<h4><?php _e( 'Slick Slider - Main params', $this->plugin_slug ); ?></h4>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'period' ) ); ?>" title="<?php _e( 'Select which deals to show.', $this->plugin_slug ); ?>"><?php echo _x( 'Promotion timeframe:', 'WidgetFields - SelectionPeriod', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'period' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'period' ) ); ?>" class="widefat">
				<option value="future" <?php selected( 'future', $instance['period'] ); ?>><?php echo _x( 'Upcoming Deals', 'WidgetFields - SelectionPeriod', $this->plugin_slug ); ?></option>
				<option value="past" <?php selected( 'past', $instance['period'] ); ?>><?php echo _x( 'Past Deals', 'WidgetFields - SelectionPeriod', $this->plugin_slug ); ?></option>
				<option value="" <?php selected( '', $instance['period'] ); ?>><?php echo _x( 'All Deals', 'WidgetFields - SelectionPeriod', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" title="<?php _e( 'Select max. number of deals to show.', $this->plugin_slug ); ?>"><?php _e( 'Max. deals to show:', $this->plugin_slug ); ?></label>
			<input type="number" max="<?php echo esc_attr( MC_Dotw::YEAR_IN_WEEKS ); ?>" min="0" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'week_num_min' ) ); ?>" title="<?php _e( 'Deals of the weeks before the selected week number won\'t be displayed.', $this->plugin_slug ); ?>"><?php _e( 'Min. deal week number:', $this->plugin_slug ); ?></label>
			<input type="number" max="<?php echo esc_attr( MC_Dotw::YEAR_IN_WEEKS - 4 ); ?>" min="0" name="<?php echo esc_attr( $this->get_field_name( 'week_num_min' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'week_num_min' ) ); ?>" value="<?php echo esc_attr( $instance['week_num_min'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'week_num_max' ) ); ?>" title="<?php _e( 'Deals of the weeks after the selected week number won\'t be displayed.', $this->plugin_slug ); ?>"><?php _e( 'Max. deal week number:', $this->plugin_slug ); ?></label>
			<input type="number" max="<?php echo esc_attr( MC_Dotw::YEAR_IN_WEEKS ); ?>" min="2" name="<?php echo esc_attr( $this->get_field_name( 'week_num_max' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'week_num_max' ) ); ?>" value="<?php echo esc_attr( $instance['week_num_max'] ); ?>"></p>
		<p>
			<?php _e( 'Include current deal:', $this->plugin_slug ); ?><br>
			<input type="radio" value="yes" <?php checked( 'yes', $instance['show_current'] ); ?> name="<?php echo esc_attr( $this->get_field_name( 'show_current' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_current' ) ); ?>"> <label for="<?php echo esc_attr( $this->get_field_id( 'show_current' ) ); ?>"> <?php _e( 'Yes', $this->plugin_slug ); ?></label>
			<input type="radio" value="" <?php checked( '', $instance['show_current'] ); ?> name="<?php echo esc_attr( $this->get_field_name( 'show_current' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_current' ) . '-2' ); ?>"> <label for="<?php echo esc_attr( $this->get_field_id( 'show_current' ) . '-2' ); ?>"> <?php _e( 'No', $this->plugin_slug ); ?></label>
		</p>
	</div><?php
        /* ---------------------------- */
        /*     Slick - More Options     */
        /* ---------------------------- */ ?>
	<hr>
	<h5><?php _e( 'Show more/less options:', $this->plugin_slug ); ?></h5>
	<input type="button" onclick="dotwToggleSlickLayout(<?php echo esc_attr( $this->number ); ?>)" value="<?php _e( 'Layout Options', $this->plugin_slug ); ?>">
	<input type="button" onclick="dotwToggleSlickCaption(<?php echo esc_attr( $this->number ); ?>)" value="<?php _e( 'Caption Options', $this->plugin_slug ); ?>">
	<input type="button" onclick="dotwToggleSlickCss(<?php echo esc_attr( $this->number ); ?>)" value="<?php _e( 'CSS Options', $this->plugin_slug ); ?>"><br>
	<hr>
	<div class="dotw-widget-admin-caption-<?php echo esc_attr( $this->number );?>" style="display:none;"><?php
        /* ---------------------------- */
        /*   Slick - Caption Settings   */
        /* ---------------------------- */ ?>
		<h4 class="dotw-slick-setup-title"><?php _e( 'Slick Slider - Caption Texts Params', $this->plugin_slug ); ?></h4>
		<input type="checkbox" value="top" <?php checked( 'top', $instance['pos_timing'] ); ?> name="<?php echo esc_attr( $this->get_field_name( 'pos_timing' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'pos_timing' ) ); ?>"> <label for="<?php echo esc_attr( $this->get_field_id( 'pos_timing' ) ); ?>"><?php _e( 'Display dates above product.', $this->plugin_slug ); ?></label><br>
		<input type="checkbox" value="top" <?php checked( 'top', $instance['pos_pricing'] ); ?> name="<?php echo esc_attr( $this->get_field_name( 'pos_pricing' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'pos_pricing' ) ); ?>"> <label for="<?php echo esc_attr( $this->get_field_id( 'pos_pricing' ) ); ?>"><?php _e( 'Display pricing above product', $this->plugin_slug ); ?></label><br>
		<input type="checkbox" value="yes" <?php checked( 'yes', $instance['flip_caption'] ); ?> name="<?php echo esc_attr( $this->get_field_name( 'flip_caption' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'flip_caption' ) ); ?>"> <label for="<?php echo esc_attr( $this->get_field_id( 'flip_caption' ) ); ?>"><?php _e( 'Display dates above prices', $this->plugin_slug ); ?></label>
		<hr>
	</div>
	<div class="dotw-widget-admin-css-<?php echo esc_attr( $this->number );?>" style="display:none;"><?php
        /* ---------------------------- */
        /*     Slick - CSS Settings     */
        /* ---------------------------- */ ?>
        <h4 class="dotw-slick-setup-title"><?php _e( 'Slick Slider - CSS Selectors', $this->plugin_slug ); ?></h4>
        <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'css_class' ) ); ?>"><?php _e( 'Custom CSS Class:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('css_class') ); ?>" name="<?php echo esc_attr( $this->get_field_name('css_class') ); ?>" value="<?php echo esc_attr($instance['css_class']); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'css_id' ) ); ?>"><?php _e( 'Custom CSS ID:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('css_id') ); ?>" name="<?php echo esc_attr( $this->get_field_name('css_id') ); ?>" value="<?php echo esc_attr($instance['css_id']); ?>"/>
		</p>
		<hr>
    </div>
	<div class="dotw-widget-admin-toggable-<?php echo esc_attr( $this->number );?>" style="display:none;"><?php
        /* ---------------------------- */
        /*    Slick - Layout Settings   */
        /* ---------------------------- */ ?>
		<h4 class="dotw-slick-setup-title"><?php _e( 'Slick Slider - Layout Params', $this->plugin_slug ); ?></h4>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'dots' ) ); ?>" title="<?php _e( 'Show dot indicators', $this->plugin_slug ); ?>"><?php _e( 'Dots:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'dots' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'dots' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['dots'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['dots'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'dotsClass' ) ); ?>" title="<?php _e( 'Class for slide indicator dots container', $this->plugin_slug ); ?>"><?php _e( 'Dots CSS Class:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'dotsClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dotsClass' ) ); ?>" value="<?php echo esc_attr( $instance['dotsClass'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'draggable' ) ); ?>" title="<?php _e( 'Enable mouse dragging.', $this->plugin_slug ); ?>"><?php _e( 'Draggable:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'draggable' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'draggable' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['draggable'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['draggable'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'fade' ) ); ?>" title="<?php _e( 'Enable fade.', $this->plugin_slug ); ?>"><?php _e( 'Fade:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'fade' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'fade' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['fade'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['fade'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'infinite' ) ); ?>" title="<?php _e( 'Infinite loop sliding.', $this->plugin_slug ); ?>"><?php _e( 'Infinite:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'infinite' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'infinite' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['infinite'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['infinite'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'initialSlide' ) ); ?>" title="<?php _e( 'Slide to start on.', $this->plugin_slug ); ?>"><?php _e( 'Initial Slide:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'initialSlide' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'initialSlide' ) ); ?>" value="<?php echo esc_attr( $instance['initialSlide'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" title="<?php _e( 'Slide/Fade animation speed.', $this->plugin_slug ); ?>"><?php _e( 'Speed:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" value="<?php echo esc_attr( $instance['speed'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'slidesToShow' ) ); ?>" title="<?php _e( '# of slides to show.', $this->plugin_slug ); ?>"><?php _e( 'Slides To Show:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'slidesToShow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'slidesToShow' ) ); ?>" value="<?php echo esc_attr( $instance['slidesToShow'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'slidesToScroll' ) ); ?>" title="<?php _e( '# of slides to scroll', $this->plugin_slug ); ?>"><?php _e( 'Slides To Scroll:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'slidesToScroll' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'slidesToScroll' ) ); ?>" value="<?php echo esc_attr( $instance['slidesToScroll'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'centerMode' ) ); ?>" title="<?php _e( 'Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.', $this->plugin_slug ); ?>"><?php _e( 'Center Mode:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'centerMode' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'centerMode' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['centerMode'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['centerMode'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'centerPadding' ) ); ?>" title="<?php _e( 'Side padding when in center mode (px or %).', $this->plugin_slug ); ?>"><?php _e( 'Center Padding:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'centerPadding' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'centerPadding' ) ); ?>" value="<?php echo esc_attr( $instance['centerPadding'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" title="<?php _e( 'Enables Autoplay.', $this->plugin_slug ); ?>"><?php _e( 'Autoplay:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['autoplay'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['autoplay'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'autoplaySpeed' ) ); ?>" title="<?php _e( 'Autoplay Speed in milliseconds.', $this->plugin_slug ); ?>"><?php _e( 'Autoplay Speed:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'autoplaySpeed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'autoplaySpeed' ) ); ?>" value="<?php echo esc_attr( $instance['autoplaySpeed'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'arrows' ) ); ?>" title="<?php _e( 'Prev/Next Arrows', $this->plugin_slug ); ?>"><?php _e( 'Arrows:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'arrows' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'arrows' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['arrows'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['arrows'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'variableWidth' ) ); ?>" title="<?php _e( 'Variable width slides.', $this->plugin_slug ); ?>"><?php _e( 'Varialble Width:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'variableWidth' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'variableWidth' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['variableWidth'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['variableWidth'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'lazyload' ) ); ?>" title="<?php _e( 'Lazy loading technique: \'ondemand\' or \'progressive\'.', $this->plugin_slug ); ?>"><?php _e( 'Lazyload:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'lazyload' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'lazyload' ) ); ?>" class="widefat">
				<option value="ondemand" <?php selected( 'ondemand', $instance['lazyload'] ); ?>><?php _e( 'On Demand', $this->plugin_slug ); ?></option>
				<option value="progressive" <?php selected( 'progressive', $instance['lazyload'] ); ?>><?php _e( 'Progressive', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vertical' ) ); ?>" title="<?php _e( 'Vertical slide mode.', $this->plugin_slug ); ?>"><?php _e( 'Vertical:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'vertical' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'vertical' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['vertical'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['vertical'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'verticalSwiping' ) ); ?>" title="<?php _e( 'Vertical swipe mode.', $this->plugin_slug ); ?>"><?php _e( 'Vertical Swiping:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'verticalSwiping' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'verticalSwiping' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['verticalSwiping'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['verticalSwiping'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'zIndex' ) ); ?>" title="<?php _e( 'Set the zIndex values for slides, useful for IE9 and lower.', $this->plugin_slug ); ?>"><?php _e( 'zIndex:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'zIndex' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'zIndex' ) ); ?>" value="<?php echo esc_attr( $instance['zIndex'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'adaptiveHeight' ) ); ?>" title="<?php _e( 'Enables adaptive height for single slide horizontal carousels.', $this->plugin_slug ); ?>"><?php _e( 'Adaptive Height:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'adaptiveHeight' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'adaptiveHeight' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['adaptiveHeight'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['adaptiveHeight'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pauseOnHover' ) ); ?>" title="<?php _e( 'Pause Autoplay On Hover.', $this->plugin_slug ); ?>"><?php _e( 'Pause On Hover:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'pauseOnHover' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'pauseOnHover' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['pauseOnHover'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['pauseOnHover'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pauseOnDotsHover' ) ); ?>" title="<?php _e( 'Pause Autoplay when a dot is hovered.', $this->plugin_slug ); ?>"><?php _e( 'Pause On Dots Hover:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'pauseOnDotsHover' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'pauseOnDotsHover' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['pauseOnDotsHover'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['pauseOnDotsHover'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'respondTo' ) ); ?>" title="<?php _e( 'Width that responsive object responds to. Can be \'window\', \'slider\' or \'min\' (the smaller of the two).', $this->plugin_slug ); ?>"><?php _e( 'Respond To:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'respondTo' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'respondTo' ) ); ?>" class="widefat">
				<option value="window" <?php selected( 'window', $instance['respondTo'] ); ?>><?php _e( 'Window', $this->plugin_slug ); ?></option>
				<option value="slider" <?php selected( 'slider', $instance['respondTo'] ); ?>><?php _e( 'Slider', $this->plugin_slug ); ?></option>
				<option value="min" <?php selected( 'min', $instance['respondTo'] ); ?>><?php _e( 'Min', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rows' ) ); ?>" title="<?php _e( 'Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.', $this->plugin_slug ); ?>"><?php _e( 'Rows:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rows' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rows' ) ); ?>" value="<?php echo esc_attr( $instance['rows'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'slidesPerRow' ) ); ?>" title="<?php _e( 'With grid mode intialized via the rows option, this sets how many slides are in each grid row.', $this->plugin_slug ); ?>"><?php _e( 'Slides Per Row:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'slidesPerRow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'slidesPerRow' ) ); ?>" value="<?php echo esc_attr( $instance['slidesPerRow'] ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'useCss' ) ); ?>" title="<?php _e( 'Enable/Disable CSS Transitions.', $this->plugin_slug ); ?>"><?php _e( 'Use Css:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'useCss' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'useCss' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['useCss'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['useCss'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'useTransform' ) ); ?>" title="<?php _e( 'Enable/Disable CSS Transforms.', $this->plugin_slug ); ?>"><?php _e( 'Use Transform:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'useTransform' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'useTransform' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['useTransform'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['useTransform'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rtl' ) ); ?>" title="<?php _e( 'Change the slider\'s direction to become right-to-left.', $this->plugin_slug ); ?>"><?php _e( 'Right-to-left:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'rtl' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'rtl' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['rtl'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['rtl'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'waitForAnimate' ) ); ?>" title="<?php _e( 'Ignores requests to advance the slide while animating.', $this->plugin_slug ); ?>"><?php _e( 'Wait For Animate:', $this->plugin_slug ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'waitForAnimate' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'waitForAnimate' ) ); ?>" class="widefat">
				<option value="true" <?php selected( 'true', $instance['waitForAnimate'] ); ?>><?php _e( 'Yes', $this->plugin_slug ); ?></option>
				<option value="false" <?php selected( 'false', $instance['waitForAnimate'] ); ?>><?php _e( 'No', $this->plugin_slug ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'touchThreshold' ) ); ?>" title="<?php _e( 'To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.', $this->plugin_slug ); ?>"><?php _e( 'Touch Threshold:', $this->plugin_slug ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'touchThreshold' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'touchThreshold' ) ); ?>" value="<?php echo esc_attr( $instance['touchThreshold'] ); ?>"/>
		</p>
		<hr>
	</div>
</div>
<?php
