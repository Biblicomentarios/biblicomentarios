/**
 * Theme Customizer enhancements for a better user experience.
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	if( 'undefined' == typeof hootInlineStyles )
		window.hootInlineStyles = [ 'hoot-style', [], '' ];

	/*** Create placeholder style tags for each setting via postMessage ***/

	if ( $.isArray( hootInlineStyles ) && hootInlineStyles[1] && $.isArray( hootInlineStyles[1] ) ) {
		var csshandle = hootInlineStyles[0] + '-inline-css';
		for ( var hi = 0; hi < hootInlineStyles[1].length; hi++ ) {
			$( '#' + csshandle ).after( '<style id="hoot-customize-' + hootInlineStyles[1][hi] + '" type="text/css"></style>' );
			csshandle = 'hoot-customize-' + hootInlineStyles[1][hi];
		}
	}

	/*** Utility ***/

	function hootUpdateCss( setting, value, append = false ) {
		var $target = $( '#hoot-customize-' + setting );
		if ( $target.length )
			if ( append ) $target.append( value ); else $target.html( value );
	}
	function hootcolor(col, amt) { // @credit: https://css-tricks.com/snippets/javascript/lighten-darken-color/
		var usePound = false;
		if (col[0] == "#") { col = col.slice(1); usePound = true; }
		var num = parseInt(col,16);
		var r = (num >> 16) + amt; if (r > 255) r = 255; else if  (r < 0) r = 0;
		var b = ((num >> 8) & 0x00FF) + amt; if (b > 255) b = 255; else if  (b < 0) b = 0;
		var g = (num & 0x0000FF) + amt; if (g > 255) g = 255; else if (g < 0) g = 0;
		return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
	}
	var hootpload = hootInlineStyles[2];

	/*** Site title and description. ***/

	wp.customize( 'blogname', function( value ) {
		value.bind( function( newval ) {
			$( '#site-logo-text #site-title a, #site-logo-mixed #site-title a' ).html( newval );
		} );
	} );

	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( newval ) {
			$( '#site-description' ).html( newval );
		} );
	} );

	/** Theme Settings **/

	wp.customize( 'site_layout', function( value ) {
		value.bind( function( newval ) {
			$( '#page-wrapper' ).removeClass('hgrid site-boxed site-stretch');
			if ( newval == 'boxed' )
				$( '#page-wrapper' ).addClass('hgrid site-boxed');
			else
				$( '#page-wrapper' ).addClass('site-stretch');
		} );
	} );

	wp.customize( 'site_width', function( value ) {
		value.bind( function( newval ) {
			var newvalint = parseInt(newval);
			if ( !newvalint || isNaN( newvalint ) ) newvalint = '1440'; // default set in customizer-options
			var css = '.hgrid { max-width: ' + newvalint + 'px }';
			hootUpdateCss( 'site_width', css );
		} );
	} );

	wp.customize( 'widgetmargin', function( value ) {
		value.bind( function( newval ) {
			var newvalint = parseInt(newval);
			if ( !newvalint || isNaN( newvalint ) ) newvalint = '';
			var css = '';
			if( newvalint ) {
				var newvalintsmall = newvalint - 15;
				css += '.main-content-grid, .widget, .frontpage-area {margin-top: '+newvalint+'px;}';
				css += '.widget, .frontpage-area {margin-bottom: '+newvalint+'px;}';
				css += '.frontpage-area.module-bg-highlight, .frontpage-area.module-bg-color, .frontpage-area.module-bg-image {padding: '+newvalint+'px 0;}';
				css += '@media only screen and (max-width: 969px) {'
					css += '.sidebar {margin-top: '+newvalint+'px;}';
					css += '.frontpage-widgetarea > div.hgrid > [class*="hgrid-span-"] {margin-bottom: '+newvalint+'px;}';
				css += '}';
				css += '.footer .widget {margin: '+newvalintsmall+'px 0;}';
				css += '.bottomborder-line:after, .bottomborder-shadow:after {margin-top: '+newvalint+'px;}';
				css += '.topborder-line:before, .topborder-shadow:before {margin-bottom: '+newvalint+'px;}';
			}
			hootUpdateCss( 'widgetmargin', css );
		} );
	} );

	wp.customize( 'logo_side', function( value ) {
		this.selectiveRefresh.bind("render-partials-response", function(response) {
			var location = '', side = ''; // var side = wp.customize( 'logo_side' ).get();
			wp.customize( 'menu_location', function( setting ) { location = setting.get(); });
			wp.customize( 'logo_side', function( setting ) { side = setting.get(); });
			if ( location == 'side' ) { location = 'none'; side = 'menu'; }
			$("#header").removeClass("header-layout-primary-menu header-layout-primary-search header-layout-primary-widget-area header-layout-primary-none header-layout-secondary-top header-layout-secondary-bottom header-layout-secondary-none").addClass("header-layout-primary-"+side+" header-layout-secondary-"+location);
			$("#header-primary").removeClass("header-primary-menu header-primary-search header-primary-widget-area header-primary-none").addClass("header-primary-"+side);
			$("#header-supplementary").removeClass("header-supplementary-top header-supplementary-bottom header-supplementary-none").addClass("header-supplementary-"+location);
		});
	} );

	wp.customize( 'fullwidth_menu_align', function( value ) {
		value.bind( function( newval ) {
			$( '#header-supplementary' ).removeClass('header-supplementary-left header-supplementary-right header-supplementary-center').addClass('header-supplementary-'+newval);
		} );
	} );

	wp.customize( 'disable_table_menu', function( value ) {
		value.bind( function( newval ) {
			if (newval) $( '#header' ).removeClass('tablemenu');
			else $( '#header' ).addClass('tablemenu');
		} );
	} );

	wp.customize( 'mobile_menu', function( value ) {
		value.bind( function( newval ) {
			$( '#menu-primary, #menu-secondary' ).removeClass('mobilemenu-inline mobilemenu-fixed').addClass('mobilemenu-'+newval);
			$( '#header-supplementary' ).removeClass('header-supplementary-mobilemenu-inline header-supplementary-mobilemenu-fixed').addClass('header-supplementary-mobilemenu-'+newval);
			if ( $('#header-aside > .menu-area-wrap').length )
				$( '#header-aside' ).removeClass('header-aside-menu-inline header-aside-menu-fixed').addClass('header-aside-menu-'+newval);
		} );
	} );

	wp.customize( 'mobile_submenu_click', function( value ) {
		value.bind( function( newval ) {
			var mobilesubmenuclass = (newval) ? 'mobilesubmenu-click' : 'mobilesubmenu-open';
			$( '#menu-primary, #menu-secondary' ).removeClass('mobilesubmenu-click mobilesubmenu-open').addClass(mobilesubmenuclass);
		} );
	} );

	wp.customize( 'below_header_grid', function( value ) {
		value.bind( function( newval ) {
			var mobilesubmenuclass = (newval == 'stretch') ? 'below-header-stretch' : 'below-header-boxed';
			$( '#below-header' ).removeClass('below-header-stretch below-header-boxed').addClass(mobilesubmenuclass);
		} );
	} );

	if(!hootpload){ wp.customize( 'logo_background_type', function( value ) {
		value.bind( function( newval ) {
			$( '#site-logo' ).removeClass('accent-typo with-background');
			if ( newval == 'accent' )
				$( '#site-logo' ).addClass('accent-typo with-background');
			// Redundant as 'logo_background_type' is not 'postMessage' in premium
			// Also, adding class/inlinestyle is not sufficient. Will also need to explicitly add inline for accent/transparent as custom background may be there in css.php if it was set when customizer loads.
			// else if ( newval == 'background' )
			// 	$( '#site-logo' ).addClass('with-background');
		} );
	} ); }

	wp.customize( 'logo_border', function( value ) {
		value.bind( function( newval ) {
			if (newval)
				$( '#site-logo' ).addClass('logo-border');
			else
				$( '#site-logo' ).removeClass('logo-border');
		} );
	} );

	if(!hootpload){ wp.customize( 'logo_size', function( value ) {
		value.bind( function( newval ) {
			$( '#site-logo-text, #site-logo-mixed' ).removeClass('site-logo-text-tiny site-logo-text-small site-logo-text-medium site-logo-text-large site-logo-text-huge').addClass( 'site-logo-text-' + newval );
		} );
	} ); }

	wp.customize( 'site_title_icon', function( value ) {
		value.bind( function( newval ) {
			if ( newval )
				$( '#site-logo-text, #site-logo-custom' ).addClass('site-logo-with-icon').find('i').remove().end().find('a').prepend('<i class="' + newval + '"></i>');
			else
				$( '#site-logo-text, #site-logo-custom' ).removeClass('site-logo-with-icon').find('i').remove();
		} );
	} );

	wp.customize( 'site_title_icon_size', function( value ) {
		value.bind( function( newval ) {
			// Doesnt include when icon is removed, or when size is changed and then icon added.
			// var $site_title_icon = $('#site-title i');
			// if ( $site_title_icon.length ) {
			// 	$site_title_icon.css('font-size',newval);
			// 	$site_title_icon.closest('#site-title').css('margin-left',newval);
			// }
			var css = '.site-logo-with-icon #site-title i { font-size: '+newval+' }';
			hootUpdateCss( 'site_title_icon_size', css );
		} );
	} );

	wp.customize( 'logo_image_width', function( value ) {
		value.bind( function( newval ) {
			var newvalint = parseInt(newval);
			if ( !newvalint || isNaN( newvalint ) ) newvalint = '150'; // default set in dynamic css.php
			var css = '.site-logo-mixed-image img { max-width: ' + newvalint + 'px }';
			hootUpdateCss( 'logo_image_width', css );
		} );
	} );

	wp.customize( 'box_background_color', function( value ) {
		value.bind( function( newval ) {
			var css = '.invert-typo' + '{color:'+newval+';}';
			css +=		'.enforce-typo'
				+','+	'#main.main, .below-header'
				+','+	'.js-search .searchform.expand .searchtext'
				+','+	'.content-block-style3 .content-block-icon'
				+'{background:'+newval+';}';
			if(!hootpload) {
				css += '.menu-items ul' + '{background:'+newval+';}';
				css += '@media only screen and (max-width: 969px) {' +
						'.mobilemenu-fixed .menu-toggle, .mobilemenu-fixed .menu-items' + '{background:'+newval+';}' +
						'}';
			} else {
				css += '.current-tabhead' + '{border-bottom-color:'+newval+';}';
			}
			hootUpdateCss( 'box_background_color', css );
		} );
	} );

	wp.customize( 'accent_color', function( value ) {
		value.bind( function( newval ) {
			var css = hoot_accent_color_css( newval );
			hootUpdateCss( 'accent_color', css );
		} );
	} );
	function hoot_accent_color_css( newval ) {
		var css = '',
			newvaldark = hootcolor( newval, -25 ),
			que = {
				"color" : [
					'body.wordpress input[type="submit"]:hover, body.wordpress #submit:hover, body.wordpress .button:hover, body.wordpress input[type="submit"]:focus, body.wordpress #submit:focus, body.wordpress .button:focus',
					'.header-aside-search.js-search .searchform i.fa-search',
					'.site-title-line em',
					'.more-link, .more-link a',
					'.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover',
					'.slider-style2 .lSAction > a:hover',
					'.widget .viewall a:hover',
					'.cta-subtitle',
					'.content-block-icon i'
					],
				"border-color" : [
					'body.wordpress input[type="submit"], body.wordpress #submit, body.wordpress .button',
					'#site-logo.logo-border',
					'.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
					'.lSSlideOuter ul.lSPager.lSpg > li a',
					'.slider-style2 .lSAction > a',
					'.icon-style-circle',
					'.icon-style-square'
					],
				"border-left-color" : [ '.widget_breadcrumb_navxt .breadcrumbs > .hoot-bcn-pretext:after' ],
				"background" : [
					'.accent-typo',
					'body.wordpress input[type="submit"], body.wordpress #submit, body.wordpress .button',
					'.site-title-line mark',
					'.sidebar .widget-title',
					'.sub-footer .widget-title, .footer .widget-title',
					'#infinite-handle span',
					'.lrm-form a.button, .lrm-form button, .lrm-form button[type=submit], .lrm-form #buddypress input[type=submit], .lrm-form input[type=submit]',
					'.widget_newsletterwidget input.tnp-submit[type=submit], .widget_newsletterwidgetminimal input.tnp-submit[type=submit]',
					'.widget_breadcrumb_navxt .breadcrumbs > .hoot-bcn-pretext',
					'.woocommerce div.product .woocommerce-tabs ul.tabs li:hover',
					'.woocommerce div.product .woocommerce-tabs ul.tabs li.active',
					'.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
					'.lSSlideOuter ul.lSPager.lSpg > li:hover a, .lSSlideOuter ul.lSPager.lSpg > li.active a',
					'.slider-style2 .lSAction > a'
					]
			},
			quedark = {
				"color" : [ '.more-link:hover, .more-link:hover a' ],
				"background" : [ '.widget_newsletterwidget input.tnp-submit[type=submit]:hover, .widget_newsletterwidgetminimal input.tnp-submit[type=submit]:hover' ]
			};
		if(!hootpload) {
			que.color.push( 'a', '.widget .view-all a:hover' ); // @deprecated <= HootKit v1.1.0 @5.20 view-all
			que.color.push( '#header-supplementary .menu-items li.current-menu-item > a, #header-supplementary .menu-items li.current-menu-ancestor > a, #header-supplementary .menu-items li:hover > a' );
			que.background.push( '#topbar', '#topbar.js-search .searchform.expand .searchtext' );
			que.background.push( '#header-supplementary', '#header-supplementary .js-search .searchform.expand .searchtext' ); // ,'#header-supplementary .menu-items ul'
			que.background.push( '.menu-items li.current-menu-item, .menu-items li.current-menu-ancestor, .menu-items li:hover' );
			quedark.color.push( 'a:hover', '.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover' );
		} else {
			que.color.push( '.wordpress .button-widget.preset-accent:hover' );
			que.background.push( '.wordpress .button-widget.preset-accent', '.notice-widget.preset-accent' );
			que['border-color'].push( '.wordpress .button-widget.preset-accent' );
			// quedark.background.push( '.wordpress .button-widget.preset-accent:hover' );
			var menu_background_type = ''; wp.customize( 'menu_background_type', function( setting ) { menu_background_type = setting.get(); });
			if ( menu_background_type == 'background' ) {
				que.color.push( '.menu-items li.current-menu-item > a, .menu-items li.current-menu-ancestor > a, .menu-items li:hover > a' );
			} else {
				que.background.push( '.menu-items li.current-menu-item, .menu-items li.current-menu-ancestor, .menu-items li:hover' );
			}
		}
		for (var prop in que) {
			if (!que.hasOwnProperty(prop)) continue;
			var selecs = que[prop];
			selecs.forEach(function(selec,selecindex){ css += selec + ','; });
			css += '.dumdum{'+prop+':'+newval+'}';
		}
		if(!hootpload) { css += '@media only screen and (max-width: 969px) { #header-supplementary .mobilemenu-fixed .menu-toggle, #header-supplementary .mobilemenu-fixed .menu-items { background: '+newval+'; } }' }
		for (var propd in quedark) {
			if (!quedark.hasOwnProperty(propd)) continue;
			var selecsd = quedark[propd];
			selecsd.forEach(function(selec,selecindex){ css += selec + ','; });
			css += '.dumdum{'+propd+':'+newvaldark+'}';
		}
		return css;
	}

	wp.customize( 'accent_font', function( value ) {
		value.bind( function( newval ) {
			var css = hoot_accent_font_css( newval );
			hootUpdateCss( 'accent_font', css );
		} );
	} );
	function hoot_accent_font_css( newval ) {
		var css = '',
			que = {
				"color" : [
					'.accent-typo',
					'body.wordpress input[type="submit"], body.wordpress #submit, body.wordpress .button',
					'.site-title-line mark',
					'.sidebar .widget-title',
					'.sub-footer .widget-title, .footer .widget-title',
					'#infinite-handle span',
					'.lrm-form a.button, .lrm-form button, .lrm-form button[type=submit], .lrm-form #buddypress input[type=submit], .lrm-form input[type=submit]',
					'.widget_newsletterwidget input.tnp-submit[type=submit], .widget_newsletterwidgetminimal input.tnp-submit[type=submit]',
					'.widget_newsletterwidget input.tnp-submit[type=submit]:hover, .widget_newsletterwidgetminimal input.tnp-submit[type=submit]:hover',
					'.widget_breadcrumb_navxt .breadcrumbs > .hoot-bcn-pretext' ,
					'.woocommerce div.product .woocommerce-tabs ul.tabs li:hover a, .woocommerce div.product .woocommerce-tabs ul.tabs li:hover a:hover',
					'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
					'.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt',
					'.sidebar .view-all-top.view-all-withtitle a, .sub-footer .view-all-top.view-all-withtitle a, .footer .view-all-top.view-all-withtitle a, .sidebar .view-all-top.view-all-withtitle a:hover, .sub-footer .view-all-top.view-all-withtitle a:hover, .footer .view-all-top.view-all-withtitle a:hover', // @deprecated <= HootKit v1.1.0 @5.20 view-all
					'.slider-style2 .lSAction > a'
					],
				"border-color" : [ ],
				"background" : [
					'body.wordpress input[type="submit"]:hover, body.wordpress #submit:hover, body.wordpress .button:hover, body.wordpress input[type="submit"]:focus, body.wordpress #submit:focus, body.wordpress .button:focus',
					'.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover', '.slider-style2 .lSAction > a:hover',
					'.widget .viewall a:hover'
					]
			};
		if(!hootpload) {
			que.color.push( '#topbar', '#topbar.js-search .searchform.expand .searchtext', '#topbar .js-search-placeholder' );
			que.color.push( '#header-supplementary', '#header-supplementary .menu-items > li > a' ); // '#header-supplementary .menu-items ul a'
			que.background.push( '#header-supplementary .menu-items li.current-menu-item, #header-supplementary .menu-items li.current-menu-ancestor, #header-supplementary .menu-items li:hover' );
			que.color.push( '.menu-items li.current-menu-item > a, .menu-items li.current-menu-ancestor > a, .menu-items li:hover > a' );
		} else {
			que.color.push( '.wordpress .button-widget.preset-accent', '.notice-widget.preset-accent' );
			que.background.push( '.wordpress .button-widget.preset-accent:hover' );
			var menu_background_type = ''; wp.customize( 'menu_background_type', function( setting ) { menu_background_type = setting.get(); });
			if ( menu_background_type == 'background' ) {
				que.background.push( '.menu-items li.current-menu-item, .menu-items li.current-menu-ancestor, .menu-items li:hover' );
			} else {
				que.color.push( '.menu-items li.current-menu-item > a, .menu-items li.current-menu-ancestor > a, .menu-items li:hover > a' );
			}
		}
		for (var prop in que) {
			if (!que.hasOwnProperty(prop)) continue;
			var selecs = que[prop];
			selecs.forEach(function(selec,selecindex){ css += selec + ','; });
			css += '.dumdum{'+prop+':'+newval+'}';
		}
		return css;
	}

	if(!hootpload){ wp.customize( 'logo_fontface_style', function( value ) {
		value.bind( function( newval ) {
			var cssvalue = (newval=='uppercase') ? 'uppercase' : 'none';
			$( '#site-title, #site-description, .site-title-line:not(.site-title-body-font,.site-title-heading-font)' ).css('text-transform',cssvalue);
		} );
	} ); }

	if(!hootpload){ wp.customize( 'headings_fontface_style', function( value ) {
		value.bind( function( newval ) {
			var cssvalue = (newval=='uppercase') ? 'uppercase' : 'none';
			var css = 'h1, h2, h3, h4, h5, h6, .title, .titlefont{text-transform:'+cssvalue+'}';
			hootUpdateCss( 'headings_fontface_style', css );
			$( '.site-title-line.site-title-heading-font' ).css('text-transform',cssvalue);
		} );
	} ); }

	// wp.customize( 'read_more', function( value ) {
	// 	value.bind( function( newval ) {
	// 		$( '.more-link a, a.more-link' ).html( newval );
	// 	} );
	// } );

	wp.customize( 'footer', function( value ) {
		value.bind( function( newval ) {
			var col = parseInt(newval.substr(0,1)),
				sty = parseInt(newval.substr(-1));
			if ( col && !isNaN( col ) && sty && !isNaN( sty ) ) {
				var fclasses = [12,12,12,12],
					fstyles = ['none','none','none','none'];
				switch (col) {
					case 1: fstyles[0] = 'block';
							break;
					case 2: if ( sty == 1 ) {      fclasses[0] = 6; fclasses[1] = 6; }
							else if ( sty == 2 ) { fclasses[0] = 4; fclasses[1] = 8; }
							else if ( sty == 3 ) { fclasses[0] = 8; fclasses[1] = 4; }
							fstyles[0] = fstyles[1] = 'block';
							break;
					case 3: if ( sty == 1 ) {      fclasses[0] = 4; fclasses[1] = 4; fclasses[2] = 4; }
							else if ( sty == 2 ) { fclasses[0] = 6; fclasses[1] = 3; fclasses[2] = 3; }
							else if ( sty == 3 ) { fclasses[0] = 3; fclasses[1] = 6; fclasses[2] = 3; }
							else if ( sty == 4 ) { fclasses[0] = 3; fclasses[1] = 3; fclasses[2] = 6; }
							fstyles[0] = fstyles[1] = fstyles[2] = 'block';
							break;
					case 4: fclasses[0] = fclasses[1] = fclasses[2] = fclasses[3] = 3;
							fstyles[0] = fstyles[1] = fstyles[2] = fstyles[3] = 'block';
							break;
				}
				$('.footer-column').removeClass('hgrid-span-12 hgrid-span-8 hgrid-span-6 hgrid-span-4 hgrid-span-3').removeAttr("style").each(function(index){
					$(this).addClass('hgrid-span-'+fclasses[index]).css('display',fstyles[index]);
				});
			}
		} );
	} );

	wp.customize( 'site_info', function( value ) {
		value.bind( function( newval ) {
			if ( newval == '<!--default-->' ) { wp.customize.selectiveRefresh.requestFullRefresh(); }
			else { newval = newval.replace("<!--year-->", new Date().getFullYear()); $( '#post-footer .credit' ).html( newval ); }
		} );
	} );

	// https://wordpress.stackexchange.com/questions/277594/customizer-selective-refresh-doesnt-refresh-properly-with-saved-value
	// https://wordpress.stackexchange.com/questions/247251/how-to-mix-partial-and-full-page-refresh-in-the-same-section-of-the-customizer
	// Syntax - transport postmessage
	// wp.customize( 'site_info', function( value ) {
	// 	value.bind( function( newval ) {
	// 		wp.customize.selectiveRefresh.requestFullRefresh(); // same as wp.customize.preview.send( 'refresh' ) @ref:wp-includes/js/customize-selective-refresh.js L#702
	// 		// Get changed value: // https://wordpress.stackexchange.com/questions/270554/accessing-customizer-values-in-javascript
	// 		console.log(wp.customize( 'logo_image_width' ).get());
	// 		wp.customize( 'logo_image_width', function( setting ) { var value = setting.get(); });
	// 		// Get Original Load Value (this still stays same even when published, hence not the 'saved' value)
	// 		console.log(wp.customize.settings.values.logo_image_width);
	// 	} );
	// } );
	// Syntax - addpartial selective_refresh
	// wp.customize( 'show_tagline', function( value ) {
	// 	this.selectiveRefresh.bind("render-partials-response", function(response) {
	// 		console.log('partial complete'); console.log(response);
	// 	});
	// } );


	var areapageid = '', areaids = [ 'area_a', 'area_b', 'area_c', 'area_d', 'area_e', 'area_f', 'area_g', 'area_h', 'area_i', 'area_j', 'area_k', 'area_l', 'content' ];
	// areaid must reach wp.customize and value.bind using $.each :: any derivative variable also needs to be calculated once areaid has reached value.bind
	$.each( areaids, function( index, areaid ) {
		wp.customize( 'frontpage_sectionbg_'+areaid+'-type', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var $area = $('#frontpage-'+areapageid), color = '', image = '', parallax = 0;
				wp.customize( 'frontpage_sectionbg_'+areaid+'-parallax', function( setting ) { parallax = setting.get(); });
				if ( $area.is('.bg-fixed.module-bg-image') || ( newval=='image' && parallax ) ) { // was parallax image or new type is image with parallax
					wp.customize.selectiveRefresh.requestFullRefresh();
				} else if ( newval == 'none' ) { // was color or image-without-parallax
					$area.removeClass('bg-fixed bg-scroll area-bgcolor').removeClass('module-bg-image module-bg-color').addClass('module-bg-none').removeAttr("style");
				} else if ( newval == 'color' ) { // was none or image-without-parallax
					wp.customize( 'frontpage_sectionbg_'+areaid+'-color', function( setting ) { color = setting.get(); });
					$area.removeClass('bg-fixed bg-scroll').removeClass('module-bg-image module-bg-none').addClass('module-bg-color area-bgcolor').removeAttr("style");
					if ( color ) $area.css('background-color',color);
				} else if ( newval == 'image' ) { // image-without-parallax: was color or none
					wp.customize( 'frontpage_sectionbg_'+areaid+'-image', function( setting ) { image = setting.get(); });
					$area.removeClass('area-bgcolor').removeClass('module-bg-color module-bg-none').addClass('module-bg-image bg-scroll').removeAttr("style");
					if ( image ) $area.css('background-image','url('+image+')');
				}
			} );
		} );
		wp.customize( 'frontpage_sectionbg_'+areaid+'-color', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var type = '';
				wp.customize( 'frontpage_sectionbg_'+areaid+'-type', function( setting ) { type = setting.get(); });
				if ( type=='color' ) $('#frontpage-'+areapageid).css('background-color',newval);
			} );
		} );
		wp.customize( 'frontpage_sectionbg_'+areaid+'-image', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var type = '', parallax = 0;
				wp.customize( 'frontpage_sectionbg_'+areaid+'-parallax', function( setting ) { parallax = setting.get(); });
				if ( parallax ) wp.customize.selectiveRefresh.requestFullRefresh();
				wp.customize( 'frontpage_sectionbg_'+areaid+'-type', function( setting ) { type = setting.get(); });
				if ( type=='image' ) {
					if (newval) $('#frontpage-'+areapageid).css('background-image','url('+newval+')');
					else $('#frontpage-'+areapageid).css('background-image','none');
				}
			} );
		} );
		wp.customize( 'frontpage_sectionbg_'+areaid+'-parallax', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var type = 0;
				wp.customize( 'frontpage_sectionbg_'+areaid+'-type', function( setting ) { type = setting.get(); });
				if ( type == 'image' ) { // refresh only if bg set to image type
					wp.customize.selectiveRefresh.requestFullRefresh();
				}
			} );
		} );
		wp.customize( 'frontpage_sectionbg_'+areaid+'-font', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var css = '', fontcolor = '';
				wp.customize( 'frontpage_sectionbg_'+areaid+'-fontcolor', function( setting ) { fontcolor = setting.get(); });
				if ( fontcolor ) { switch (newval) {
					case 'color': css = '.frontpage-'+areapageid+' *, .frontpage-'+areapageid+' .more-link, .frontpage-'+areapageid+' .more-link a {color:'+fontcolor+'}'; break;
					case 'force': css = '#frontpage-'+areapageid+' *, #frontpage-'+areapageid+' .more-link, #frontpage-'+areapageid+' .more-link a {color:'+fontcolor+'}'; break;
				} }
				hootUpdateCss( 'frontpage-'+areapageid, css );
			} );
		} );
		wp.customize( 'frontpage_sectionbg_'+areaid+'-fontcolor', function( value ) {
			value.bind( function( newval ) {
				areapageid = ( areaid == 'content' ) ? 'page-content' : areaid;
				var css = '', font = '';
				wp.customize( 'frontpage_sectionbg_'+areaid+'-font', function( setting ) { font = setting.get(); });
				switch (font) {
					case 'color': css = '.frontpage-'+areapageid+' *, .frontpage-'+areapageid+' .more-link, .frontpage-'+areapageid+' .more-link a {color:'+newval+'}'; break;
					case 'force': css = '#frontpage-'+areapageid+' *, #frontpage-'+areapageid+' .more-link, #frontpage-'+areapageid+' .more-link a {color:'+newval+'}'; break;
				}
				hootUpdateCss( 'frontpage-'+areapageid, css );
			} );
		} );
	} );

} )( jQuery );