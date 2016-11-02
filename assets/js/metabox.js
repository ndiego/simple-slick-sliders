jQuery(document).ready(function($){

	/* General Metabox scripts
	-------------------------------------------------------------- */

	// Show the selected content type
	function show_selected_content() {
		$('.sss-content-type').each( function() {
			var content_type = $(this).val();

			// All content sections start as hidden, so show the one selected
			$( this ).parents( '.sss-content-type-container' ).siblings( '.sss-content-' + content_type ).removeClass( 'sss-hidden' );
		});
	};

	// Run on page load so selected content is visible
	show_selected_content();

	// Shows and hides each content type on selection
	$( document ).on( 'change', '.sss-content-type', function(){
		var content_type = $(this).val();

		$( this ).parents( '.sss-content-type-container' ).siblings().addClass( 'sss-hidden' );
		$( this ).parents( '.sss-content-type-container' ).siblings( '.sss-content-' + content_type ).removeClass( 'sss-hidden' );
	});



	/* Modal scripts
	-------------------------------------------------------------- */

	// Close the modal if you click on the overlay
	$(document).on( 'click', '#sss_overlay', function() {
		$( '#sss_overlay' ).fadeOut(200);
		$( '.sss-modal' ).css({ 'display' : 'none' });
	});

	// Close the modal if you click on close button
	$(document).on( 'click', '.sss-modal-close', function() {
		$( '#sss_overlay' ).fadeOut(200);
		$( '.sss-modal' ).css({ 'display' : 'none' });
	});


	/* Content - Slideshow scripts
	-------------------------------------------------------------- */

	$('.sss-slideshow-type').each( function() {
		var slideshow_type = $(this).val();

		$(this).parents( '.sss-slideshow-type-container' ).siblings( '.sss-slideshow-option' ).addClass( 'sss-hidden' );

		// All content sections start as hidden, so show the one selected
		$( this ).parents( '.sss-content-slideshow' ).find( '.sss-content-slideshow-' + slideshow_type ).removeClass( 'sss-hidden' );
	});

	// Shows and hides each slideshow type on selection
	$(document).on( 'change', '.sss-slideshow-type', function(){
		var slideshow_type = $(this).val();

		$(this).parents( '.sss-slideshow-type-container' ).siblings( '.sss-slideshow-option' ).addClass( 'sss-hidden' );
		$(this).parents( '.sss-slideshow-type-container' ).siblings( '.sss-content-slideshow-' + slideshow_type ).removeClass( 'sss-hidden' );

	});

	// Slideshow Uploader function
	sss_builtinSlideshowUpload = {

		// Call this from the upload button to initiate the upload frame.
		uploader : function( name_prefix ) {
			var frame = wp.media({
				id : name_prefix, // We set the id to be the name_prefix so that we can save our slides
				title : sss_localize_metabox_scripts.slideshow_media_title,
				multiple : true,
				library : { type : 'image' }, //only can upload images
				button : { text : sss_localize_metabox_scripts.slideshow_media_button }
			});

			// Handle results from media manager
			frame.on( 'select', function() {


					// We are on global so we don't worry about targeting
					var select_target ='.sss-slider-container';


				var selection = frame.state().get( 'selection' );

				// Need this to handle multiple images selected
				selection.map( function( attachment ) {
					attachment = attachment.toJSON();
					$( select_target ).append( function() {

						// Generate a unique id for each slide: From http://stackoverflow.com/questions/6248666/how-to-generate-short-uid-like-ax4j9z-in-js
						var randSlideId = 'slide_' + ("0000" + (Math.random()*Math.pow(36,4) << 0).toString(36)).slice(-4);
						var output = '';

						output += '<li id="' + randSlideId + '" class="sss-slideshow-item" >';
						output += '<div class="sss-slide-container"><image  src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.alt + '" /></div>';
						output += '<input type="text" class="slide-image-id sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][slide_type]" value="image" />';
						output += '<input type="text" class="slide-image-id sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][id]" value="' + attachment.id + '" />';
						output += '<input type="text" class="slide-image-url sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][url]" value="' + attachment.url + '" />';
						output += '<input type="text" class="slide-image-title sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][title]" value="' + attachment.title + '" />';
						output += '<input type="text" class="slide-image-alt sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][alt]" value="' + attachment.alt + '" />';
						output += '<input type="checkbox" class="slide-image-link-enable sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][link][enable]" value="1" />';
						output += '<input type="text" class="slide-image-link-url sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][link][url]" value="http://" />';
						output += '<input type="text" class="slide-image-link-title sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][link][title]" value="" />';
						output += '<input type="checkbox" class="slide-image-link-target sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][link][target]" value="1" />';
						output += '<input type="text" class="slide-image-caption sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][caption]" value="' + attachment.caption + '" />';
						output += '<input type="text" class="slide-image-classes sss-force-hidden" name="' + frame.id + '[standard][slides]['+ randSlideId +'][image][classes]" value="" />';
						output += '<div class="sss-slide-details-container"><a class="sss-slide-details" href="#sss_slide_details">' + sss_localize_metabox_scripts.slideshow_details + '</a><a class="sss-slide-remove" href="#">' + sss_localize_metabox_scripts.slideshow_remove + '</a></div>';
						output += '</li>';

						return output;
					});
				});

				// If our filler slide is present, remove it!
				if ( $('.sss-filler').length > 0 ) {
					$('.sss-filler').remove();
				}

			});

			frame.open();
			return false;
		},

	};

	// Remove Slideshow Items
	// Need to '.on' because we are working with dynamically generated content
	$(document).on( 'click', '.sss-slider-container .sss-slide-remove', function() {

		var message = confirm( sss_localize_metabox_scripts.slideshow_confirm_remove );

		if ( message == true ) {

			var block_id = $(this).parents( '.sss-content-block' ).attr( 'id' );

			// If we are on a global block, the retrieved block id will be gibberish and not be a number. But if we are on a global block we don't need to worry about targeting...
			if ( ! isNaN( block_id ) ) {
				// We are on local so we need to target using the block id
				var block_id = '#' + block_id;
			} else {
				// We are on global so we don't worry about targeting
				var block_id = '';
			}

			// Now that we have retrieved the block id, remove the slide
			$(this).parents( '.sss-slideshow-item' ).remove();

			// If we remove the slide and there are no more, show our filler slide
			if ( $( block_id + ' .sss-filler').length == 0 && $( block_id + ' .sss-slideshow-item' ).length == 0 ) {
				$( block_id + ' .sss-slider-container' ).append( '<li class="sss-filler" ><div class="sss-filler-container"></div><div class="sss-filler-text"><span>' + sss_localize_metabox_scripts.slideshow_details + '</span><span class="right">' + sss_localize_metabox_scripts.slideshow_remove + '</span></div></li>' );
			}
			return false;
		} else {
			// Makes the browser not shoot to the top of the page on "cancel"
			return false;
		}
	});

	// Make Slideshow Items sortable
	$( '.sss-slider-container' ).sortable({
		items: '.sss-slideshow-item',
		cursor: 'move',
		forcePlaceholderSize: true,
		placeholder: 'placeholder'
	});

	// Common function for both Galleries and Slideshows
	// Display the slide details modal (need .on because new slides are dynamically added to the page)
	// Code is a heavily modified version of http://leanmodal.finelysliced.com.au
	$(document).on( 'click', '.sss-slide-details', function(e) {

		e.preventDefault();

		var $is_gallery = $( this ).parents( 'li' ).hasClass( 'sss-gallery-item' );

		if ( $is_gallery ) {
			//alert('this is a gallery');
		}

		// Add the overlay to the page and style on click
		var overlay = $( '<div id="sss_overlay"></div>' );
		$( 'body' ).append(overlay);
		$( '#sss_overlay' ).css( { 'display' : 'block', 'opacity' : 0 } );
		$( '#sss_overlay' ).fadeTo( 200, 0.7 );

		// Add the modal to the page and style on click
		var modal_id = "#sss_slide_details";
		var modal_height = $( modal_id ).outerHeight();
		var modal_width = $( modal_id ).outerWidth();
		$( modal_id ).css({
			'display' : 'block',
			'position' : 'fixed',
			'opacity' : 0,
			'z-index': 110000,
			'top' : 30 + 'px',
			'bottom' : 30 + 'px',
			'left' : 30 + 'px',
			'right' : 30 + 'px'

			// Old Styling
			//'left' : 50 + '%',
			//'margin-left' : -(modal_width/2) + "px",
			//'top' : 40 + "%",
			//'margin-top' : -(modal_height/2) + "px"
		});
		$( modal_id ).fadeTo( 200, 1 );

		// Grab our existing slide details
		var id 			= $( this ).parents( 'li' ).attr( 'id' );
		var title 		= $( '#' + id + ' .slide-image-title' ).attr( 'value' );
		var alt 		= $( '#' + id + ' .slide-image-alt' ).attr( 'value' );
		var caption 	= $( '#' + id + ' .slide-image-caption' ).attr( 'value' );
		var link_enable = $( '#' + id + ' .slide-image-link-enable' ).is( ':checked' );
		var link_url 	= $( '#' + id + ' .slide-image-link-url' ).attr( 'value' );
		var link_title 	= $( '#' + id + ' .slide-image-link-title' ).attr( 'value' );
		var link_target = $( '#' + id + ' .slide-image-link-target' ).is( ':checked' );
		var classes 	= $( '#' + id + ' .slide-image-classes' ).attr( 'value' );

		// Populate the modal with existing details on open
		$( '.modal-slide-id' ).attr( 'value' , id );
		$( '.modal-slide-image-title' ).attr( 'value' , title );
		$( '.modal-slide-image-alt' ).attr( 'value' , alt );
		$( '.modal-slide-image-caption' ).attr( 'value' , caption );
		$( '.modal-slide-image-link-enable' ).prop( 'checked', link_enable );
		$( '.modal-slide-image-link-url' ).attr( 'value' , link_url );
		$( '.modal-slide-image-link-title' ).attr( 'value' , link_title );
		$( '.modal-slide-image-link-target' ).prop( 'checked', link_target );
		$( '.modal-slide-image-classes' ).attr( 'value' , classes );

		// If the image link is enabled, show the additional options
		if ( $( '.modal-slide-image-link-enable' ).is( ':checked' ) ) {
		  	$( '.modal-slide-image-link-enable' ).parents( '.sss-image-link-enable' ).siblings( '.sss-image-link' ).show();
		}

		// Gallery specific settings
		if ( $is_gallery ) {
			var width = $( '#' + id + ' .slide-width' ).attr( 'value' );
			var height = $( '#' + id + ' .slide-height' ).attr( 'value' );

			$( '.modal-slide-width' ).attr( 'value' , width );
			$( '.modal-slide-height' ).attr( 'value' , height );
		}


		// Add our new details to the slide on button click
		// Need to use .data() otherwise won't work due to dynamic targeting issue
		$(document).data( 'slide-metadata', { ids: id }).on( 'click', '#sss-apply-details', function() {
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-title' ).val( $( '.modal-slide-image-title' ).val() );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-alt' ).val( $( '.modal-slide-image-alt' ).val() );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-caption' ).val( $( '.modal-slide-image-caption' ).val() );

			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-link-enable' ).prop( 'checked', $( '.modal-slide-image-link-enable' ).is( ':checked' ) );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-link-url' ).val( $( '.modal-slide-image-link-url' ).val() );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-link-title' ).val( $( '.modal-slide-image-link-title' ).val() );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-link-target' ).prop( 'checked', $( '.modal-slide-image-link-target' ).is( ':checked' ) );
			$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-image-classes' ).val( $( '.modal-slide-image-classes' ).val() );

			// Gallery specific settings
			if ( $is_gallery ) {
				$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-width' ).val( $( '.modal-slide-width' ).val() );
				$( '#' + $( document ).data( "slide-metadata" ).ids + ' .slide-height' ).val( $( '.modal-slide-height' ).val() );
			}

			$( "#sss_overlay" ).fadeOut(200);
			$( modal_id ).css( { 'display' : 'none' } );
		});

		// Close the modal if you click on the overlay
		$(document).on( 'click', '#sss_overlay', function() {
			$( "#sss_overlay" ).fadeOut(200);
			$( modal_id ).css( { 'display' : 'none' } );
		});

		// Close the modal if you click on close button
		$(document).on( 'click', '.sss-modal-close', function() {
			$( "#sss_overlay" ).fadeOut(200);
			$( modal_id ).css( { 'display' : 'none' } );
		});

	});






	/* Helper Text scripts
	-------------------------------------------------------------- */

	// Show/Hide help text when (?) is clicked
	var helpIcon = window.helpIcon = {

		toggleHelp : function( el ) {
			$( el ).parent().siblings( '.sss-help-text' ).slideToggle( 'fast' );
			return false;
		}
	}


    // On global blocks, preserve current tab on save an on page refresh
    if ( $( 'body' ).hasClass( 'post-type-simple-slick-sliders' ) ) {
        var sss_tabs_hash 	    = window.location.hash,
            sss_tabs_hash_sani = window.location.hash.replace('!', '');

        // If we have a hash and it begins with "sss_tab_", set the proper tab to be opened.
        if ( sss_tabs_hash && sss_tabs_hash.indexOf( 'sss_tab_' ) >= 0 ) {
            $( '.sss-tab-navigation li' ).removeClass( 'current' );
            $( '.sss-tab-navigation' ).find( 'li a[href="' + sss_tabs_hash_sani + '"]' ).parent().addClass( 'current' );
            $( '.sss-tabs-container' ).children().hide();
            $( '.sss-tabs-container' ).children( sss_tabs_hash_sani ).show();

            // Update the post action to contain our hash so the proper tab can be loaded on save.
            var post_action = $( '#post' ).attr( 'action' );
            if ( post_action ) {
                post_action = post_action.split( '#' )[0];
                $( '#post' ).attr( 'action', post_action + sss_tabs_hash );
            }
        }
    }

    // Show desired tab on click
    $(document).on( 'click', '.sss-tab-navigation a', function(e) {
        e.preventDefault();

        if ( $( this ).parent().hasClass( 'current' ) ) {
            return;
        } else {
            // Adds current class to active tab heading
            $( this ).parent().addClass( 'current' );
            $( this ).parent().siblings().removeClass( 'current' );

            var tab = $( this ).attr( 'href' );

            //if ( $( this ).parents( '.sss-settings-tabs' ).hasClass( 'global' ) ) {

            // We add the ! so the addition of the hash does not cause the page to jump
            window.location.hash = $( this ).attr( 'href' ).split( '#' ).join( '#!' );

            // Update the post action to contain our hash so the proper tab can be loaded on save.
            var post_action = $( '#post' ).attr( 'action' );
            if ( post_action ) {
                post_action = post_action.split('#')[0];
                $( '#post' ).attr( 'action', post_action + window.location.hash );
            }

            //}

            // Show the correct tab
            $(this).parents( '.sss-tab-navigation' ).siblings( '.sss-tabs-container' ).children( '.sss-tab-content' ).not( tab ).hide();
            $(this).parents( '.sss-tab-navigation' ).siblings( '.sss-tabs-container' ).children( tab ).show();
        }

    });


});
