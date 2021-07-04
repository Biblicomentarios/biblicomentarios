jQuery(document).ready(function($) {
	"use strict";

	if( 'undefined' == typeof hootData )
		window.hootData = {};

	/*** Superfish Navigation ***/

	if( 'undefined' == typeof hootData.superfish || 'enable' == hootData.superfish ) {
		if (typeof $.fn.superfish != 'undefined') {
			$('.sf-menu').superfish({
				delay: 500,						// delay on mouseout 
				animation: {height: 'show'},	// animation for submenu open. Do not use 'toggle' #bug
				animationOut: {opacity:'hide'},	// animation for submenu hide
				speed: 200,						// faster animation speed 
				speedOut: 'fast',				// faster animation speed
				disableHI: false,				// set to true to disable hoverIntent detection // default = false
			});
		}
	}

	/*** Responsive Navigation ***/

	if( 'undefined' == typeof hootData.menuToggle || 'enable' == hootData.menuToggle ) {
		var $html = $('html');
		if ( $('#wpadminbar').length ) $html.addClass('has-adminbar');
		$( '.menu-toggle' ).click( function(event) {
			event.preventDefault();
			var $menuToggle = $(this),
				$navMenu = $menuToggle.parent(),
				$menuItems = $menuToggle.siblings('.menu-items'),
				isFixedMenu = $navMenu.is('.mobilemenu-fixed');
			$menuToggle.toggleClass( 'active' );
			if ( isFixedMenu ) {
				$html.toggleClass( 'fixedmenu-open' );
				$navMenu.toggleClass( 'mobilemenu-open' ); // Redundant as css animation moved to js: used for z-index in themes with dual menus BMUMBDNx
				if( $menuToggle.is('.active') ) {
					$menuItems.show().css( 'left', '-' + $menuItems.outerWidth() + 'px' ).animate( {left:0}, 300 );
					$menuToggle.animate( { left: $menuItems.width() + 'px' }, 300 );
				} else {
					$menuItems.animate( { left: '-' + $menuItems.outerWidth() + 'px' }, 300, function(){ $menuItems.hide(); } );
					$menuToggle.animate( { left: '0' }, 300 );
				}
			} else {
				$menuItems.css( 'left', 'auto' ).slideToggle(); // add left:auto to override inline left from fixed menu in customizer screen: added for brevity only
			}
		});
		$('body').click(function (e) {
			if ( $html.is('.fixedmenu-open') && !$(e.target).is( '.nav-menu *, .nav-menu' ) )
				$( '.menu-toggle.active' ).click();
		});
	}

	/*** Mobile menu - Modal Focus ***/
	// @todo: fix for themes with dual menus BMUMBDNx
	// @todo: bugfix: when $lastItem does not have href, focus shifts from <a> with href to
	//                next element in document i.e. outside menu (doesnt even close it)
	function keepFocusInMenu(){
		var _doc = document,
			$menu = $('.nav-menu'),
			$menuels = $menu.find( 'input, a, button' ),
			$toggle = $menu.children('.menu-toggle'),
			$lastItem = $menu.find('.menu-items > li > a');
		_doc.addEventListener( 'keydown', function( event ) {
			if ( window.matchMedia( '(max-width: 969px)' ).matches ) {
				var activeEl = _doc.activeElement,
					toggle = $toggle[0],
					menuOpen = $toggle.is('.active'),
					lastEl = $menuels[ $menuels.length - 1 ],
					lastItem = $lastItem[0],
					tabKey = event.keyCode === 9,
					shiftKey = event.shiftKey;
				if ( tabKey && !shiftKey && lastEl === activeEl ) {
					event.preventDefault();
					toggle.focus();
				}
				if ( tabKey && shiftKey && toggle === activeEl && menuOpen ) {
					event.preventDefault();
					$lastItem.focus();
				}
			}
		});
	}
	keepFocusInMenu();

	/*** JS Search ***/

	$('.js-search i.fa-search, .js-search .js-search-placeholder').each(function(){
		var $self = $(this),
			searchform = $self.parent('.searchform');
		$self.on('click', function(){
			searchform.toggleClass('expand');
			if ( searchform.is('.expand' ) ) {
				$self.siblings('input.searchtext').focus();
				searchform.closest('.js-search').addClass('hasexpand');
			} else {
				searchform.closest('.js-search').removeClass('hasexpand');
			}
		});
	});
	$('.js-search .searchtext').each(function(){
		var $self = $(this),
			searchform = $self.parent('.searchform');
		$self.on({
			focus: function() { searchform.addClass('expand').closest('.js-search').addClass('hasexpand'); },
			blur: function() { searchform.removeClass('expand').closest('.js-search').removeClass('hasexpand'); }
		});
	});

	/*** Responsive Videos : Target your .container, .wrapper, .post, etc. ***/

	if( 'undefined' == typeof hootData.fitVids || 'enable' == hootData.fitVids ) {
		if (jQuery.fn.fitVids)
			$("#content").fitVids();
	}

	/*** Theia Sticky Sidebar ***/

	if( 'undefined' == typeof hootData.stickySidebar || 'enable' == hootData.stickySidebar ) {
		if (jQuery.fn.theiaStickySidebar && $('.hoot-sticky-sidebar .main-content-grid > #content').length && $('.hoot-sticky-sidebar .main-content-grid > .sidebar').length) {
			var stickySidebarTop = 10;
			if ( $('.hoot-sticky-header #header').length )
				stickySidebarTop = 100;
			if( 'undefined' != typeof hootData && 'undefined' != typeof hootData.stickySidebarTop )
				stickySidebarTop = hootData.stickySidebarTop;
			$( '#content, #sidebar-primary, #sidebar-secondary' ).theiaStickySidebar({
				additionalMarginTop: stickySidebarTop,
			});
		}
	}

});