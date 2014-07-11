jQuery(document).ready(function(){	

	jQuery(".listing-location").click(function(e) {


		var value = jQuery(this).attr('data-value');

		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	jQuery.post(

	 		the_ajax_script.ajaxurl, {

	 			action : 'test_response',
                'location': value,
                'active': 'yes'

	 		}, 

	 		function(response) {

	 	});

		var index = jQuery(this).attr('data-index');
		var list = jQuery('#location'+ index);

		jQuery(".category").slideUp();

		if( list.css( 'display' ) == "none" ) {
			
			list.slideDown();
		
		} else {

			list.slideUp();
		}

	});

	jQuery(".listing-location-home").click(function(e) {

		var value = jQuery(this).attr('data-value');

		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	jQuery.post(

	 		the_ajax_script.ajaxurl, {

	 			action : 'test_response',
                'location': value,
                'active': 'yes'

	 		}, 

	 		function(response) {

	 	});

	});

	jQuery(".tabs a").click(function(event) {

        event.preventDefault();
        jQuery(this).parent().addClass("current");
        jQuery(this).parent().siblings().removeClass("current");
        var tab = jQuery(this).attr("href");
        jQuery(".tab-content").not(tab).css("display", "none");
        jQuery(tab).fadeIn();
    
    });

	jQuery("a.categorylist").click(function(){

	   jQuery("a.categorylist.active").removeClass("active");
	   jQuery(this).addClass("active");

	});
	
});
