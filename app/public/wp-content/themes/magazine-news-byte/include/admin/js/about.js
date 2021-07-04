jQuery(document).ready(function($) {
	"use strict";

	$('.hoot-abouttheme-top').on('click',function(e){
		var $target = $( $(this).attr('href') );
		if ( $target.length ) {
			e.preventDefault();
			var destin = $target.offset().top - 50;
			$("html:not(:animated),body:not(:animated)").animate({ scrollTop: destin}, 500 );
		}
	});

	$('.hoot-abouttabs .nav-tab, .hoot-about-sub .linkto-tab, .hoot-abouttabs .linkto-tab').on('click',function(e){
		e.preventDefault();
		var targetid = $(this).data('tabid'),
			$navtabs = $('.hoot-abouttabs .nav-tab'),
			$tabs = $('.hoot-abouttabs .hoot-tab'),
			$target = $('#hoot-'+targetid);
		if ( $target.length ) {
			$navtabs.removeClass('nav-tab-active');
			$navtabs.filter('[data-tabid="'+targetid+'"]').addClass('nav-tab-active');
			$tabs.removeClass('hoot-tab-active');
			$target.addClass('hoot-tab-active');
			var destin = $target.offset().top - 150;
			$("html:not(:animated),body:not(:animated)").animate({ scrollTop: destin}, 200 );
		}
	});

	$('#hoot-welcome-msg .notice-dismiss').on('click',function(e){
		if( 'undefined' != typeof hoot_dismissible_notice && 'undefined' != typeof hoot_dismissible_notice.nonce && 'undefined' != typeof hoot_dismissible_notice.ajax_action ) {
			// e.preventDefault();
			jQuery.ajax({
				url : ajaxurl, // hoot_dismissible_notice.ajax_url
				type : 'post',
				data : {
					'action': hoot_dismissible_notice.ajax_action,
					'nonce': hoot_dismissible_notice.nonce
				},
				success : function( response ) {}
			}); //$.post(ajaxurl, data);
		}
	});

});