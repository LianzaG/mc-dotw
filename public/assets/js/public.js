(function ( $ ) {
	"use strict";

	$(function () {
		$j('.dotw-w-slicklist').slick(
			// Other params are set using 'data-slick' attribute on HTML elements.
			// @see /admin/includes/class-mc-dotw-admin-widget.php : function widget().
		    // responsive: [
		    //   {
		    //      breakpoint: 1024,
		    //      settings: {
		    //        slidesToShow: 1,
		    //        slidesToScroll: 1,
		    //        infinite: true
		    //     }
		    //   },
		    //   {
		    //     breakpoint: 600,
		    //     settings: {
		    //       slidesToShow: 1,
		    //       slidesToScroll: 1
		    //     }
		    //   },
		    //   {
		    //   breakpoint: 480,
		    //   settings: {
		    //       slidesToShow: 1,
		    //       slidesToScroll: 1
		    //     }
		    //   }
		      // You can unslick at a given breakpoint now by adding:
		      // settings: "unslick"
		      // instead of a settings object
		    // ]
		);
	});

}(jQuery));
