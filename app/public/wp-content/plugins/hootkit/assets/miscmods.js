jQuery(document).ready(function($) {
	"use strict";

	if( 'undefined' == typeof hootData )
		window.hootData = {};

	/****** WC Carticon ******/

	if( 'undefined' == typeof hootData.carticonrefresh || 'enable' == hootData.carticonrefresh ) {
		// $( document.body ).on( 'wc_fragment_refresh updated_wc_div', function() {});
		$( document.body ).on( 'added_to_cart removed_from_cart', function( event, fragments, cart_hash ) {
			// e.preventDefault();
			var $carticons = $('.carticon-widget'),
				$cartitems = $carticons.find('.carticon-cartitems'),
				$cartvalue = $carticons.find('.carticon-cartvalue'),
				nonce      = $carticons.attr('data-nonce');
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : hootkitMiscmodsData.ajaxurl,
				data : {action: 'hk_carticon_refresh', nonce: nonce},
				beforeSend: function() {
					$carticons.addClass('hk-refreshing');
				},
				success: function(response) {
					$carticons.removeClass('hk-refreshing');
					if ( response.hasitems == 'yes' ) {
						$carticons.removeClass('announce-nomsg');
						$cartitems.removeClass('no-cartitems').html(response.items);
						$cartvalue.removeClass('no-cartvalue').html(response.cartvalue);
					} else {
						$carticons.addClass('announce-nomsg');
						$cartitems.addClass('no-cartitems').html('');
						$cartvalue.addClass('no-cartvalue').html('');
					}
				}
			});
		});
	}

	/****** WC Fly Cart ******/
	if( 'undefined' == typeof hootData.flycart || 'enable' == hootData.flycart ) {
		var $html = $('html');
		if ( $('#wpadminbar').length ) $html.addClass('has-adminbar');
		$( '.flycart-toggle' ).click( function(event) {
			event.preventDefault();
			var $flycartToggle = $(this),
				$flycart = $flycartToggle.parent(),
				$flycartPanel = $flycartToggle.siblings('.flycart-panel'),
				isLeft = $flycart.is('.flycart-left');
			$flycartToggle.toggleClass( 'active' );
			$html.toggleClass( 'flycart-open' );
			if( $flycartToggle.is('.active') ) {
				if ( isLeft ) {
					$flycartPanel.show().css( 'left', '-' + $flycartPanel.outerWidth() + 'px' ).animate( {left:0}, 300 );
					$flycartToggle.animate( { left: $flycartPanel.width() + 'px' }, 300 );
				} else {
					$flycartPanel.show().css( 'right', '-' + $flycartPanel.outerWidth() + 'px' ).animate( {right:0}, 300 );
					$flycartToggle.animate( { right: $flycartPanel.width() + 'px' }, 300 );
				}
			} else {
				if ( isLeft ) {
					$flycartPanel.animate( { left: '-' + $flycartPanel.outerWidth() + 'px' }, 300, function(){ $flycartPanel.hide(); } );
					$flycartToggle.animate( { left: '0' }, 300 );
				} else {
					$flycartPanel.animate( { right: '-' + $flycartPanel.outerWidth() + 'px' }, 300, function(){ $flycartPanel.hide(); } );
					$flycartToggle.animate( { right: '0' }, 300 );
				}
			}
		});
		$('body').click(function (e) {
			if ( $html.is('.flycart-open') && !$(e.target).is( '.fly-cart *, .fly-cart' ) ) {
				$( '.flycart-toggle.active' ).click();
			}
		});

		var $flycart = $('#fly-cart'),
			flycarttimeout = 1000;
		if( 'undefined' != typeof hootData && 'undefined' != typeof hootData.flycarttimeout )
			flycarttimeout = hootData.flycarttimeout;
		if ( $flycart.is('.flycart-showonadd') ) {
			$( document.body ).on( 'added_to_cart removed_from_cart', function( event, fragments, cart_hash ) {
				$( '.flycart-toggle:not(.active)' ).click();
				setTimeout(function() { $( '.flycart-toggle.active' ).click(); }, 1500);
			});
		}
	}
	/****** WC Fly Cart - Modal Focus : @todo ******/

	/****** Timer ******/

	if( 'undefined' == typeof hootData.timer || 'enable' == hootData.timer ) {
		$('.hootkit-timer').each(function(){
			var $self = $(this),
				selfData = $self.data();
			if ( 'undefined' == typeof selfData.diff ) return; // customizer view
			var timeCounter = setInterval(function() {
					var display = '';
					if ( selfData.diff < 0 ) {
						clearInterval(timeCounter);
						display += '<span class="timer-expired">' + selfData.expiredlabel + '</span>';
						// console.log( 'expired' );
					} else {
						// error due to 30/31 days: eg end date 1m3d vs 1y1m3d from now => use JS Date class
						// var years = Math.floor( selfData.diff / ( 60 * 60 * 24 * 365 ) ); 
						// var months =  Math.floor( ( selfData.diff % ( 60 * 60 * 24 * 365 ) ) / ( 60 * 60 * 24 * 31 ) );
						// var days =    Math.floor( ( selfData.diff % ( 60 * 60 * 24 * 31  ) ) / ( 60 * 60 * 24 ) );
						var days    = Math.floor( selfData.diff / ( 60 * 60 * 24 ) );
						var hours   = Math.floor( ( selfData.diff % ( 60 * 60 * 24       ) ) / ( 60 * 60 ) );
						var minutes = Math.floor( ( selfData.diff % ( 60 * 60            ) ) / ( 60      ) );
						var seconds = Math.floor( ( selfData.diff % ( 60                 ) )               );
						// if ( years == 1 ) display += years + ' ' + selfData.yearlabel + ' ';
						// if ( years > 1 ) display += years + ' ' + selfData.yearslabel + ' ';
						// if ( months == 1 ) display += months + ' ' + selfData.monthlabel + ' ';
						// if ( months > 1 ) display += months + ' ' + selfData.monthslabel + ' ';
						if ( days == 1 ) display += '<span class="days-count">' + days + '</span> <span class="days-label">' + selfData.daylabel  + '</span> ';
						if ( days > 1 ) display  += '<span class="days-count">' + days + '</span> <span class="days-label">' + selfData.dayslabel + '</span> ';
						display += '<span class="hours-count">'   + ( "0" + hours ).slice(-2)   + '</span>';
						display += '<span class="minutes-count">' + ( "0" + minutes ).slice(-2) + '</span>';
						display += '<span class="seconds-count">' + ( "0" + seconds ).slice(-2) + '</span>' ;
						// console.log( selfData.diff+' '+years+' '+ months+' '+days+' '+hours+' '+minutes+' '+seconds );
					}
					$self.html( display );
					selfData.diff = selfData.diff - 1;
				}, 1000);
		});
	}

});