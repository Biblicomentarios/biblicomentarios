(function($){

	"use strict";

	var file_frame;

	$( 'body' ).on( 'click', '.hoot_term_img_add', function( event ) {
		event.preventDefault();
		if ( file_frame ) {
			file_frame.open();
			return;
		}
		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: 'Choose a Fetured Image',
			button: {
				text: 'Use as Fetured Image'
			},
			multiple: false
		});
		file_frame.on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON(),
				attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full,
				$image = $( '#hoot_term_img' );
			$image.val( attachment.id );
			$image.siblings('img').attr( 'src', attachment_thumbnail.url );
		});
		file_frame.open();
	});

	$( 'body' ).on( 'click', '.hoot_term_img_remove', function() {
		var $image = $( '#hoot_term_img' ),
			$img = $image.siblings('img');
		$image.val( '' );
		$img.attr( 'src', $img.attr('data-placeholder') );
		return false;
	});

}(jQuery));