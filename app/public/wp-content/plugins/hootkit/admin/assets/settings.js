jQuery(document).ready(function($) {
	"use strict";

	if( 'undefined' == typeof hootkitData )
		window.hootkitData = {};

	var $modChecks = $('.hk-mods input[type=checkbox]'),
		$modSetsChecks = $('.hk-modset-title input[type=checkbox]');

	$('.hk-mod > span.modname').click( function(e){
		$(this).siblings('.hk-toggle-box').children('.hk-toggle').click();
	});
	$('.hk-toggle').click( function(e){
		e.preventDefault();
		var $self = $(this),
			$control = $self.siblings('input[type=checkbox]'),
			$modsetTtile = $self.closest('.hk-modset-title'),
			toggled = ( $control.prop( "checked" ) ) ? false : true;
		if ( $modsetTtile.length ) {
			/* Modset Title toggle */
			$modsetTtile.siblings('.hk-mods').find('.hk-toggle-box input[type=checkbox]').prop( "checked", toggled );
			$control.trigger('click');
		} else {
			/* Mod toggle */
			// $self.closest('.hk-mods').siblings('.hk-modset-title').find('.hk-toggle-box input[type=checkbox]').prop( "checked", true );
			// $control.trigger('click');
			$modChecks.filter('[value=' + $control.val() + ']').each(function(){
				$(this).prop( "checked", toggled );
				// Check Modset Title in case it is unchecked
				if ( toggled ) $(this).closest('.hk-mods').siblings('.hk-modset-title').find('.hk-toggle-box input[type=checkbox]').prop( "checked", true );
			});
		}
	});

	$('#hk-enableall').click( function(e){
		e.preventDefault();
		$modChecks.prop( "checked", true );
		$modSetsChecks.prop( "checked", true );
	});

	$('#hk-disableall').click( function(e){
		e.preventDefault();
		$modChecks.prop( "checked", false );
		$modSetsChecks.prop( "checked", false );
	});

	$('#hk-submit').click( function(e){
		e.preventDefault();

		var $submit = $(this),
			$form = $('#hootkit-settings'),
			$feedback = $('#hkfeedback'),
			formvalues = $form.serialize();

		if ( $submit.is('.disabled') )
			return;

		$form.addClass('hksdisabled');
		$submit.addClass('disabled');
		$feedback.hide();

		$.ajax({
			method: 'POST',
			url: hootkitData.ajaxurl, // url with nonce GET param
			data: { 'handle' : 'setactivemodules', 'values' : formvalues },
			success: function( data ){
				// console.log(data);
				if ( data.setactivemodules == true ) {
					feedback( $feedback, 'success', hootkitData.strings.success );
				} else {
					var msg = ( 'undefined' !== typeof data.msg ) ? data.msg : hootkitData.strings.error;
					feedback( $feedback, 'error', msg );
				}
			},
			error: function( data ){
				feedback( 'error', hootkitData.strings.error );
			},
			complete: function( data ){
				$form.removeClass('hksdisabled');
				$submit.removeClass('disabled');
			}
		});

	});

	function feedback( $feedback, context, string ) {
		$feedback.html( string ).removeClass('hkfberror hkfbsuccess').addClass('hkfb'+context).fadeIn().delay(1500).fadeOut();
	}

});