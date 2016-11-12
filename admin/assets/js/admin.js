(function ( $j ) {
	"use strict";

	$j(function () {
		/*
	     * Trigger suggest.js AJAX autocomplete on selected DOM elements:
	     * autocomplete a week's deal product selection field with suggestions
	     * from our WooCommerce catalog.
	     */
	    $j(".dotw_deals_suggest").suggest(
	    	ajaxurl + "?action=dotw_deals_suggest_options",
	    	{
	    		delay: 500,
	    		minchars: 2
	    	}
	    );

	    /*
	     * When the deal's product selection field changes:
	     * update price fields with new infos + update accordion title.
	     */
	    $j(".dotw_deals_suggest").on('change', function() {

	        var userInput  = $j( this ).attr( 'value' )
	            , id       = $j( this ).attr( 'id' )
	        	, week_num = id.match( /(\d+)/g )[0];
	        /*
	         * User can type any character to perform the ajax product lookup but,
	         * once a json response has been selected, the following condition will
	         * evaluate to TRUE.
	         */
	        if ( /^{"title":"/.test( userInput) ) {
	            // debugger;
	            // Parse the selected json deal option.
	            var response     = $j.parseJSON(userInput)
	              , title        = response.title
	              , product_id   = response.product_id;


	            // We won't store the selection as JSON so, now that the info was retrieved and parsed,
	            // we can set the value of $j( this ) to the title of the selected product.
	            $j( this ).attr( 'value', title );
	            // Insert product_id inside the form.
	            $j('#' + id + '_product_id').attr('value', product_id);

	            /*
	             * Fetch and insert all the prices of the newly selected product (asynchronous XMLHttpRequest).
	             */
	            $j.get(
	    		    ajaxurl,
	    		    {
	    		    	action: 'dotw_deals_option_meta',
	    		    	title: title,
	    		    	product_id: product_id,
	    		    	week_num: week_num
	    		    },
	    		    function(data) {

	                    data = $j.parseJSON(data);

	    		    	$j('#' + id + '_regular_price').attr('value', data.regular_price);
	    		    	$j('#' + id + '_sale_price').attr('value', data.sale_price);
	    		    	$j('#' + id + '_hot_price').attr('value', data.hot_price);
	                    $j('#' + id + "_hot_price").attr( 'placeholder', data.sale_price );
	                    $j('#' + id + '_date_from').attr('value', data.date_from);
	                    $j('#' + id + '_date_to').attr('value', data.date_to);

	                    $j('#' + id + '_aside').html(data.aside_html);
	    		    }
	    		);

	            /*
	             * Update this deal's accordion title to reflect the new selection.
	             */

	            // >> First insert selection inside the deal's title.
	            $j('#' + id + '_title .cur_selection').html( title );

	            // >> Then update title anchor's css class to further reflect the deal's state.
	            if ( title.length > 0 ) {
	                $j('#' + id + '_title' ).removeClass( 'muted' );
	            } else {
	                $j('#' + id + '_title' ).addClass( 'muted' );
	            }

	        } else if ( '' === userInput || userInput.length == 0 ) {
	        	// console.log(id);
	        	// debugger;
	            // Reset hidden data field if the deal selection is removed from the input.
	            $j('#' + id + '_product_id').attr('value', '');

	            $j('#' + id + '_regular_price').attr('value', '');
	            $j('#' + id + '_sale_price').attr('value', '');
	            $j('#' + id + '_hot_price').attr('value', '');
	            $j('#' + id + '_date_from').attr('value', '');
	            $j('#' + id + '_date_to').attr('value', '');

	            /*
	             * Update this deal's accordion title to reflect the cleared selection.
	             */
	            // >> First clear the deal's title.
	            $j('#' + id + '_title .cur_selection').html( '' );
	            // >> Then update title anchor's css class to further reflect the deal's state.
	            $j('#' + id + '_title' ).addClass( 'muted' );

	            // Clear tha aside div.
	            $j('#' + id + '_aside').html( '' );
	        }

	    });

	    // Accordion
	    $j("#accordion, #accordion-side").accordion({ header: "h3" });

	    // Datepicker
	    $j('.datepicker_from, .datepicker_to').datepicker({
	        inline: false,
	        dateFormat: "yy-mm-dd"
	    });

	    // Change a deal's 'edited' status to 'Y' when any of its 'wc_product related fields' is changed.
	    $j('input[id^="dotw_deals_"]').on('change', function() {

	        var            id = $j( this ).attr( 'id' );
	        var weekNumPrefix = id.match( /dotw_deals_(\d+)/g )[0];

	        // Don't signal the change if the fields are not WooCommerce relevant (mainDealSelection or hotPriceSetting)
	        var bypassChangeEvent = id === weekNumPrefix || id === weekNumPrefix + '_hot_price';

	        if( true || ! bypassChangeEvent ) {
	            $j('#' + weekNumPrefix + "_wc_data_edited").attr( 'value', 'Y' );
	        }
	    });

	    // Update sale_price and hot_price placeholders when each one's counterpart changes.
	    $j('input[id^="dotw_deals_"]').on('change', function() {

	        var            id = $j( this ).attr( 'id' );
	        var weekNumPrefix = id.match( /dotw_deals_(\d+)/g )[0];

	        if ( id === weekNumPrefix + '_sale_price' ) {
	            $j('#' + weekNumPrefix + "_hot_price").attr( 'placeholder', $j( this ).val() );
	        } else if ( id === weekNumPrefix + '_hot_price' ) {
	            $j('#' + weekNumPrefix + "_sale_price").attr( 'placeholder', $j( this ).val() );

	        } else if ( id === weekNumPrefix + '_regular_price' ) {

	        }
	    });

	    /**
	     * Widget admin UI callback.
	     *
	     * Triggered when changing the widget's admin form's 'output format' value.
	     *
	     * @since 1.0.2
	     */
	    $j( '[id^="widget-dotw_widget"][id$="format"]' ).on( 'change', function() {
	    	// Widget's 'output format' form field and number.
	    	var sSelectID = $j( this ).attr( 'id' )
	    	        , num = sSelectID.match( /(\d+)/g )[0];

	    	// Slick Slider's options wrapper div.
	    	var slickDiv = $j( 'div.widget-dotw_widget-' + num + '-slick-wrapper' ) ;

	    	// Show when slick is selected, hide otherwise.
	    	if ( 'slick' == $j( this ).val() ) {
	    		slickDiv.slideDown();
	    	} else {
	    		slickDiv.slideUp();
	    	}
	    });
	});
}(jQuery));

/**
 * Toggle a widget's admin form 'Slick Layout Settings' section.
 *
 * @since   1.0.2
 *
 * @param   {string}  id  Numeric string identifying the widget on which to take action.
 *
 * @return  {void}
 */
function dotwToggleSlickLayout( id ) {
	$j( '.dotw-widget-admin-toggable-' + id ).toggle();

}

/**
 * Toggle a widget's admin form 'Caption Texts Settings' section.
 *
 * @since   1.0.2
 *
 * @param   {string}  id  Numeric string identifying the widget on which to take action.
 *
 * @return  {void}
 */
function dotwToggleSlickCaption( id ) {
	$j( '.dotw-widget-admin-caption-' + id ).toggle();
}

/**
 * Toggle a widget's admin form 'CSS Settings' section.
 *
 * @since   1.0.3
 *
 * @param   {string}  id  Numeric string identifying the widget on which to take action.
 *
 * @return  {void}
 */
function dotwToggleSlickCss( id ) {
	$j( '.dotw-widget-admin-css-' + id ).toggle();
}
