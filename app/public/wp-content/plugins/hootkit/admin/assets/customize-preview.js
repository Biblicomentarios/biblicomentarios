/**
 * Theme Customizer Preview
 */

( function( $ ) {

	wp.customize( 'hktb_background', function( value ) {
		value.bind( function( newval ) {
			if ( newval )
				$('.topbanner-wrap').removeClass('topbanner-nobg').addClass('topbanner-hasbg').css('background-image','url('+newval+')');
			else
				$('.topbanner-wrap').removeClass('topbanner-hasbg').addClass('topbanner-nobg').css('background-image','none');
		} );
	} );

	wp.customize( 'hktb_url', function( value ) {
		value.bind( function( newval ) {
			if ( newval ) {
				$('.topbanner-wrap').removeClass('hide-contenturl');
				$('.topbanner-url').attr('href',newval);
			} else {
				$('.topbanner-wrap').addClass('hide-contenturl');
				$('.topbanner-url').attr('href','#');
			}
		} );
	} );

	wp.customize( 'hktb_url_target', function( value ) {
		value.bind( function( newval ) {
			if ( newval ) {
				$('.topbanner-url').attr('target','_blank');
			} else {
				$('.topbanner-url').attr('target','_self');
			}
		} );
	} );

	wp.customize( 'hktb_content_stretch', function( value ) {
		value.bind( function( newval ) {
			$('.topbanner-wrap').removeClass('topbanner-content-stretch topbanner-content-grid').addClass('topbanner-content-'+newval);
		} );
	} );

	wp.customize( 'hktb_content_nopad', function( value ) {
		value.bind( function( newval ) {
			if ( newval )
				$('.topbanner-wrap').addClass('topbar-content-nopad');
			else
				$('.topbanner-wrap').removeClass('topbar-content-nopad');
		} );
	} );

	wp.customize( 'hktb_content_bg', function( value ) {
		value.bind( function( newval ) {
			$('.topbanner-contentbox').removeClass('style-dark style-light style-dark-on-light style-light-on-dark').addClass('style-'+newval);
		} );
	} );

	wp.customize( 'hkfc_icon', function( value ) {
		value.bind( function( newval ) {
			$('.flycart-toggle i').removeClass().addClass(newval);
			$('.flycart-topicon i').removeClass().addClass(newval);
		} );
	} );

	wp.customize( 'hkfc_location', function( value ) {
		value.bind( function( newval ) {
			$('#fly-cart').removeClass('flycart-left flycart-right').addClass('flycart-'+newval);
		} );
	} );

	wp.customize( 'hkfc_showonadd', function( value ) {
		value.bind( function( newval ) {
			if ( newval )
				$('#fly-cart').addClass('flycart-showonadd');
			else
				$('#fly-cart').removeClass('flycart-showonadd');
		} );
	} );

} )( jQuery );