/*
 * Dashicons Icon Picker
 * http://themergency.com/dashicon-picker-jquery-plugin/
 * https://github.com/bradvin/dashicons-picker
 *
 * Copyright 2013 Themergency.com
 * Licensed under the GPLv2 license.
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Author: Brad Vincent (@themergency) 
 
 
 * Add info code inspiration Site Logo and Simple Image Widget
 */

(function($) {

	$.fn.imageUploader = function( options ) {

		return this.each( function() {
			
			var $button = $(this);
			var Attachment = wp.media.model.Attachment;

			$button.on('click', function(e) {
				e.preventDefault();
				createPopup($button);
			});

			function createPopup($button) {
			
				if ( ! this.frame )
					initFrame();

				frame.open();
			}
			
			function initFrame() {
			
				frame = wp.media({
					// The title of the media modal
					title: "Choose Image",
					// restrict to specified mime type
					library: {
						type: "image"
					},
					// Customize the submit button.
					button: {
						// Set the text of the button.
						text: "Choose Image"
					},
					// Just one, thanks.
					multiple: false
				});
				
				// Update the selected image in the media library based on the image in the control.
				frame.on( 'open', function() {
					var selection = this.get( 'library' ).get( 'selection' ),
						attachment, ids;

					if ( $($button.data('target')).length ) {
						ids = $($button.data('target')).val();
						
						if ( ids && '' !== ids && -1 !== ids && '0' !== ids ) {
							attachment = Attachment.get( ids );
							attachment.fetch();
						}
					}

					selection.reset( attachment ? [ attachment ] : [] );
				});

				// When an image is selected, run a callback.
				frame.on('select', function() {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					attachment = reduceMembers( attachment );
					
					$($button.data('target')).val(attachment.id).trigger("change");
					$($button.data('preview')).attr("src", attachment.sizes.medium.url);

				});

			}
			
			function reduceMembers( attachment ) {
				var desired = [ 'id', 'sizes', 'url' ];
				var output = {};
				$.each( desired, function( i, key ){
					output[key] = attachment[key];
				});
				return output;
			}

		});
	}
	
	function initImageUploader( widget ) {
		widget.find( '.msw-image-uploader' ).imageUploader();
	}

	function onFormUpdate( event, widget ) {
		initImageUploader( widget );
	}

	$( document ).on( 'widget-added widget-updated widget-synced', onFormUpdate );

	$( document ).ready( function() {
		$( '#widgets-right .widget:has(.msw-image-uploader)' ).each( function () {
			initImageUploader( $( this ) );
		} );
	} );

}(jQuery));