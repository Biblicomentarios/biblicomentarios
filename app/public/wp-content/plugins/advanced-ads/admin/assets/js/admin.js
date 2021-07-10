jQuery( document ).ready( function ( $ ) {
	function advads_load_ad_type_parameter_metabox ( ad_type ) {
		jQuery( '#advanced-ad-type input' ).prop( 'disabled', true )
		$( '#advanced-ads-tinymce-wrapper' ).hide()
		$( '#advanced-ads-ad-parameters' ).html( '<span class="spinner advads-ad-parameters-spinner advads-spinner"></span>' )
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'load_ad_parameters_metabox',
				'ad_type': ad_type,
				'ad_id': $( '#post_ID' ).val(),
				'nonce': advadsglobal.ajax_nonce
			},
			success: function ( data, textStatus, XMLHttpRequest ) {
				// toggle main content field.
				if ( data ) {
					$( '#advanced-ads-ad-parameters' ).html( data ).trigger( 'paramloaded' )
					advads_maybe_textarea_to_tinymce( ad_type )
				}
			},
			error: function ( MLHttpRequest, textStatus, errorThrown ) {
				$( '#advanced-ads-ad-parameters' ).html( errorThrown )
			}
		} ).always( function ( MLHttpRequest, textStatus, errorThrown ) {
			jQuery( '#advanced-ad-type input' ).prop( 'disabled', false )
		} )
	}

	$( document ).on( 'click', '#switch-to-adsense-type', function ( ev ) {
		ev.preventDefault()
		AdvancedAdsAdmin.AdImporter.adsenseCode = Advanced_Ads_Admin.get_ad_source_editor_text()
		$( '#advanced-ad-type-adsense' ).trigger( 'click' )
		$( this ).closest( 'li' ).addClass( 'hidden' )
	} )

	$( document ).on( 'change', '#advanced-ad-type input', function () {
		var ad_type = $( this ).val()
		advads_load_ad_type_parameter_metabox( ad_type )
	} )

	// trigger for ad injection after ad creation
	$( '#advads-ad-injection-box .advads-ad-injection-button' ).on( 'click', function () {
		var placement_type = this.dataset.placementType, // create new placement
				placement_slug = this.dataset.placementSlug, // use existing placement
				options        = {}

		if ( ! placement_type && ! placement_slug ) { return }

		// create new placement
		if ( placement_type ) {
			// for content injection
			if ( 'post_content' === placement_type ) {
				var paragraph = prompt( advadstxt.after_paragraph_promt, 1 )
				if ( paragraph !== null ) {
					options.index = parseInt( paragraph, 10 )
				}
			}
		}
		$( '#advads-ad-injection-box .advads-loader' ).show()
		$( '#advads-ad-injection-box-placements' ).hide()
		$( 'body' ).animate( { scrollTop: $( '#advads-ad-injection-box' ).offset().top - 40 }, 1, 'linear' )

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'advads-ad-injection-content',
				placement_type: placement_type,
				placement_slug: placement_slug,
				ad_id: $( '#post_ID' ).val(),
				options: options,
				nonce: advadsglobal.ajax_nonce
			},
			success: function ( r, textStatus, XMLHttpRequest ) {
				if ( ! r ) {
					$( '#advads-ad-injection-box' ).html( 'an error occured' )
					return
				}

				$( '#advads-ad-injection-box *' ).hide()
				// append anchor to placement message
				$( '#advads-ad-injection-message-placement-created .advads-placement-link' ).attr( 'href', $( '#advads-ad-injection-message-placement-created a' ).attr( 'href' ) + r )
				$( '#advads-ad-injection-message-placement-created, #advads-ad-injection-message-placement-created *' ).show()
			},
			error: function ( MLHttpRequest, textStatus, errorThrown ) {
				$( '#advads-ad-injection-box' ).html( errorThrown )
			}
		} ).always( function ( MLHttpRequest, textStatus, errorThrown ) {
			// jQuery( '#advanced-ad-type input').prop( 'disabled', false );
		} )
	} )

	// activate general buttons
	$( '.advads-buttonset' ).advads_buttonset()
	// activate accordions
	if ( $.fn.accordion ) {
		$( '.advads-accordion' ).accordion( {
			active: false,
			collapsible: true,
		} )
	}

	/**
	 * Logic for ad groups
	 */

	// display new ad group form
	$( '#advads-new-ad-group-link' ).on( 'click', function ( e ) {
		e.preventDefault()
		$( '#advads-new-group-form' ).show().find( 'input[type="text"]' ).focus()
	} )

    // display ad groups form
	$( '#advads-ad-group-list a.edit, #advads-ad-group-list a.row-title' ).on( 'click', function ( e ) {
        e.preventDefault();
        var advadsgroupformrow = $(this).parents('.advads-group-row').next('.advads-ad-group-form');
        if (advadsgroupformrow.is(':visible')) {
            advadsgroupformrow.addClass('hidden');
            // clear last edited id
            $('#advads-last-edited-group').val('');
        } else {
            advadsgroupformrow.removeClass('hidden');
            var group_id = advadsgroupformrow.find('.advads-group-id').val()
            $('#advads-last-edited-group').val(group_id);
            // remember that we opened that one
            advadsgroupformrow.data('touched', true);
        }
    });
    // display ad groups usage
	$( '#advads-ad-group-list a.usage' ).on( 'click', function ( e ) {
        e.preventDefault();
        var usagediv = $(this).parents('.advads-group-row').find('.advads-usage');
        if (usagediv.is(':visible')) {
            usagediv.addClass('hidden');
        } else {
            usagediv.removeClass('hidden');
        }
    });
	// handle the submission of the groups form
	$( 'form#advads-form-groups' ).on( 'submit', function () {
		jQuery( 'tr.advads-ad-group-form' ).each( function ( k, v ) {
			v = jQuery( v )
			if ( ! v.data( 'touched' ) ) {
				v.remove()
			}
		} )
	} )

	/**
	 * Logic for placement list
	 */
	// filter placement by type
	jQuery( '.advads_filter_placement_type' ).on( 'change', function () {
		var selectedValue = jQuery( this ).val();
		if ( selectedValue === '0' ) {
			jQuery( '.advads-placements-table tbody > tr' ).show();
		} else {
			jQuery( '.advads-placements-table tbody > tr' ).each( function () {
				if ( jQuery( this ).data( 'type' ) !== selectedValue ) {
					jQuery( this ).hide();
				} else {
					jQuery( this ).show();
				}
			} );
		}
	} );

	// search placement by name
	jQuery( '.advads_search_placement_name' ).on( 'keyup', function () {
		jQuery( '.advads_filter_placement_type option[value=\'0\']' ).attr( 'selected', true );
		var value = this.value;
		jQuery( '.advads-placements-table' ).find( 'tr' ).each( function ( index ) {
			if ( ! index ) {
				return;
			}
			var name = jQuery( this ).data( 'name' );
			if ( typeof name !== 'undefined' ) {
				jQuery( this ).toggle( name.toLowerCase().indexOf( value.toLowerCase() ) !== - 1 );
			}
		} );
	} );

	jQuery( '.advads-modal-close-action' ).on( 'click', function () {
		// Change url hash, so that the modal doesn't load after refresh again.
		window.location.hash = '#close';
		jQuery( '#advanced-ads-placements-form' ).submit();
	} );

	jQuery( '.advads-delete-tag' ).each( function () {
		jQuery( this ).on( 'click', function () {
			var r = confirm( 'Wirklich löschen?' );
			if ( r === true ) {
				var row = jQuery( this ).parents( '.advanced-ads-placement-row' );
				row.find( '.advads-placements-item-delete' ).prop( 'checked', true );
				row.data( 'touched', true );
				jQuery( '#advanced-ads-placements-form' ).submit();
			}
		} );
	} );

	// sort placement by type or name
	jQuery( '.advads-sort' ).on( 'click', function ( e ) {
		var sort  = jQuery( this );
		var order = sort.data( 'order' );
		var table = jQuery( '.advads-placements-table' );
		var rows  = jQuery( '> tbody > tr', table );
		var links = jQuery( '> thead th > a', table );
		links.each( function () {
			jQuery( this ).removeClass( 'advads-placement-sorted' );
		} );
		sort.addClass( 'advads-placement-sorted' );
		rows.sort( function ( a, b ) {
			var keyA = jQuery( a ).data( order );
			var keyB = jQuery( b ).data( order );
			return ( keyA.localeCompare( keyB ) );
		} );
		jQuery.each( rows, function ( index, row ) {
			table.append( row );
		} );
		sort.data( 'order', order );
		var state = {order: order};
		var url   = window.location.pathname + window.location.search;

		if ( url.indexOf( 'orderby=' ) !== - 1 ) {
			url = url.replace( /\borderby=[0-9a-zA-Z_@.#+-]{1,50}\b/, 'orderby=' + order );
		} else {
			url += '&orderby=' + order;
		}
		window.history.replaceState( state, document.title, url );
		e.preventDefault();
	} );

	// show warning if Container ID option contains invalid characters
	$( '#advads-output-wrapper-id' ).on( 'keyup', function () {
		var id_value = $( this ).val()
		if ( /^[a-z-0-9]*$/.test( id_value ) ) {
			$( '.advads-output-wrapper-id-error' ).removeClass( 'advads-error-message' )
		} else {
			$( '.advads-output-wrapper-id-error' ).addClass( 'advads-error-message' ).css( 'display', 'block' )
		}
	} )
	/**
	 * Automatically open all options and show usage link when this is the placement linked in the URL
	 * also highlight the box with an effect for a short time.
	 * Use attribute selector to avoid the need to escape the selector.
	 */
	function sanitize( string ) {
		var map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			'\'': '&#x27;',
			'/': '&#x2F;'
		};
		return string.replace( /[&<>"'/]/ig, function ( match ) {
			return map[match];
		} );
	}

	var single_placement_slug = '#' + sanitize( window.location.hash.substr( 1 ) );
	if ( jQuery( single_placement_slug ).length ) {
		jQuery( single_placement_slug ).find( '.advads-toggle-link + div, .advads-usage' ).show();
	}

	// group page: add ad to group
	$( '.advads-group-add-ad button' ).on( 'click', function () {
		var $settings_row = $( this ).closest( '.advads-ad-group-form' ),
				$ad           = $settings_row.find( '.advads-group-add-ad-list-ads option:selected' )
		$weight_selector = $settings_row.find( '.advads-group-add-ad-list-weights' ).last(),
			$ad_table = $settings_row.find( '.advads-group-ads tbody' )
		// add new row if does not already exist
		if ( $ad.length && $weight_selector.length && ! $ad_table.find( '[name="' + $ad.val() + '"]' ).length ) {
			$ad_table.append(
				$( '<tr></tr>' ).append(
					$( '<td></td>' ).html( $ad.text() ),
					$( '<td></td>' ).append( $weight_selector.clone().val( $weight_selector.val() ).prop( 'name', $ad.val() ) ),
					'<td><button type="button" class="advads-remove-ad-from-group button">x</button></td>'
				)
			)
		}
	} )
	// group page: remove ad from group
	$( '#advads-ad-group-list' ).on( 'click', '.advads-remove-ad-from-group', function () {
		var $ad_row = $( this ).closest( 'tr' )

		if ( $ad_row.data( 'ad-id' ) ) {
			// save the ad id, it is needed when this ad is not included in any other group
			$( '#advads-ad-group-list form' ).append(
				'<input type="hidden" name="advads-groups-removed-ads[]" value="' + $ad_row.data( 'ad-id' ) + '">'
				+ '<input type="hidden" name="advads-groups-removed-ads-gid[]" value="' + $ad_row.data( 'group-id' ) + '">'
			)
		}
		$ad_row.remove()
	} )
	// group page: handle switching of group types based on a class derrived from that type
	$( '.advads-ad-group-type input' ).on( 'click', function () {
		advads_show_group_options( $( this ) )
	} )

	function advads_show_group_options ( el ) {
		// first, hide all options except title and type
		// iterate through all elements
		el.each( function () {
			var _this = jQuery( this )
			_this.parents( '.advads-ad-group-form' ).find( '.advads-option:not(.static)' ).hide()
			var current_type = _this.val()

			// now, show only the ones corresponding with the group type
			_this.parents( '.advads-ad-group-form' ).find( '.advads-group-type-' + current_type ).show()
		} )
	}

	// set default group options for earch group

	advads_show_group_options( $( '.advads-ad-group-type input:checked' ) )
	// group page: hide ads if more than 4 – than only show 3
	$( '.advads-ad-group-list-ads' ).each( function () {
		if ( 5 <= $( this ).find( 'li' ).length ) {
			$( this ).find( 'li:gt(2)' ).hide()
		}

	} )
	// show more than 3 ads when clicked on a link
	$( '.advads-group-ads-list-show-more' ).on( 'click', function () {
		jQuery( this ).hide().parents( '.advads-ad-group-list-ads' ).find( 'li' ).show()
	} )

	/**
	 * SETTINGS PAGE
	 */

	// automatically copy the first entered license key into all other empty fields
	$( '.advads-settings-tab-main-form .advads-license-key' ).on( 'blur', function () {
		// get number of license fields

		var license_key = $( this ).val()

		if ( '' === license_key ) {
			return
		}

		var license_fields               = $( '.advads-settings-tab-main-form .advads-license-key' )
		var license_fields_without_value = []

		// count license fields without value
		license_fields.each( function ( i, el ) {
			if ( '' === $( el ).val() ) {
				license_fields_without_value.push( el )
			}
		} )

		// if there is only one field filled then take its content (probably a license key) and add it into the other fields
		if ( license_fields.length === (license_fields_without_value.length + 1) ) {
			$.each( license_fields_without_value, function ( i, el ) {
				$( el ).val( license_key )
			} )
		}

	} )

	// activate licenses
	$( '.advads-license-activate' ).on( 'click', function () {

		var button = $( this )

		if ( ! this.dataset.addon ) { return }

		advads_disable_license_buttons( true )

		var query = {
			action: 'advads-activate-license',
			addon: this.dataset.addon,
			pluginname: this.dataset.pluginname,
			optionslug: this.dataset.optionslug,
			license: $( this ).parents( 'td' ).find( '.advads-license-key' ).val(),
			security: $( '#advads-licenses-ajax-referrer' ).val()
		}

		// show loader
		$( '<span class="spinner advads-spinner"></span>' ).insertAfter( button )

		// send and close message
		$.post( ajaxurl, query, function ( r ) {
			// remove spinner
			$( 'span.spinner' ).remove()
			var parent = button.parents( 'td' )

			if ( r === '1' ) {
				parent.find( '.advads-license-activate-error' ).remove()
				parent.find( '.advads-license-deactivate' ).show()
				button.fadeOut()
				parent.find( '.advads-license-activate-active' ).fadeIn()
				parent.find( 'input' ).prop( 'readonly', 'readonly' )
				advads_disable_license_buttons( false )
			} else if ( r === 'ex' ) {
				var input = parent.find( 'input.advads-license-key' )
				var link  = parent.find( 'a.advads-renewal-link' )
				if ( input && link ) {
					var license_key = input.val()
					var href        = link.prop( 'href' )
					link.prop( 'href', href.replace( '%LICENSE_KEY%', license_key ) )
				}
				parent.find( '.advads-license-activate-error' ).remove()
				parent.find( '.advads-license-expired-error' ).show()
				advads_disable_license_buttons( false )
			} else {
				parent.find( '.advads-license-activate-error' ).show().html( r )
				advads_disable_license_buttons( false )
			}
		} )
	} )

	// deactivate licenses
	$( '.advads-license-deactivate' ).on( 'click', function () {

		var button = $( this )

		if ( ! this.dataset.addon ) { return }

		advads_disable_license_buttons( true )

		var query = {
			action: 'advads-deactivate-license',
			addon: this.dataset.addon,
			pluginname: this.dataset.pluginname,
			optionslug: this.dataset.optionslug,
			security: $( '#advads-licenses-ajax-referrer' ).val()
		}

		// show loader
		$( '<span class="spinner advads-spinner"></span>' ).insertAfter( button )

		// send and close message
		$.post( ajaxurl, query, function ( r ) {
			// remove spinner
			$( 'span.spinner' ).remove()

			if ( r === '1' ) {
				button.siblings( '.advads-license-activate-error' ).hide()
				button.siblings( '.advads-license-activate-active' ).hide()
				button.siblings( '.advads-license-activate' ).show()
				button.siblings( 'input' ).prop( 'readonly', false )
				button.fadeOut()
				advads_disable_license_buttons( false )
			} else if ( r === 'ex' ) {
				button.siblings( '.advads-license-activate-error' ).hide()
				button.siblings( '.advads-license-activate-active' ).hide()
				button.siblings( '.advads-license-expired-error' ).show()
				button.siblings( 'input' ).prop( 'readonly', false )
				button.fadeOut()
				advads_disable_license_buttons( false )
			} else {
				console.log( r )
				button.siblings( '.advads-license-activate-error' ).show().html( r )
				button.siblings( '.advads-license-activate-active' ).hide()
				advads_disable_license_buttons( false )
			}
		} )
	} )

	// toggle license buttons – disable or not
	function advads_disable_license_buttons ( disable = true ) {
		var buttons = $( 'button.advads-license-activate, button.advads-license-deactivate' ) // all activation buttons
		// disable all buttons to prevent issues when users try to enable multiple licenses at the same time
		if ( disable ) {
			buttons.attr( 'disabled', 'disabled' )
		} else {
			buttons.removeAttr( 'disabled' )
		}
	}

	/**
	 * PLACEMENTS
	 */
	// show image tooltips
	$( '.advanced-ads_page_advanced-ads-placements .advads-placement-type' ).advads_tooltip( {
		content: function () {
			return jQuery( this ).find( '.advads-placement-description' ).html()
		}
	} )

	var set_touched_placement = function() {
		var tr = $( this ).closest( 'tr.advanced-ads-placement-row' )
		if ( tr ) {
			tr.data( 'touched', true )
		}
	}

	//  keep track of placements that were changed
	$( 'form#advanced-ads-placements-form input, #advanced-ads-placements-form select' ).on( 'change', set_touched_placement )
	$( 'form#advanced-ads-placements-form button' ).on( 'click', set_touched_placement )

	//  some special form elements overwrite the jquery listeners (or render them unusable in some strange way)
	//  to counter that and make it more robust in general, we now listen for mouseover events, that will
	//  only occur, when the settings of a placement are expanded (let's just assume this means editing)
	$( 'form#advanced-ads-placements-form .advads-modal' ).on( 'mouseover', set_touched_placement )

	//  on submit remove placements that were untouched
	$( 'form#advanced-ads-placements-form' ).on( 'submit', function () {
		var grouprows = jQuery( 'form#advanced-ads-placements-form tr.advanced-ads-placement-row' )
		jQuery( 'form#advanced-ads-placements-form tr.advanced-ads-placement-row' ).each( function ( k, v ) {
			v = jQuery( v )
			if ( ! v.data( 'touched' ) ) {
				v.find( 'input, select' ).each( function ( k2, v2 ) {
					v2 = jQuery( v2 )
					v2.prop( 'disabled', true )
				} )
			}
		} )
	} )

	// show input field for custom xpath rule when "custom" option is selected for Content placement
	// iterate through all tag options of all placements
	$( '.advads-placements-content-tag' ).each( function(){
		advads_show_placement_content_xpath_field( this );
	})
	// update xpath field when tag option changes
	$( '.advads-placements-content-tag' ).on( 'change', function () {
		advads_show_placement_content_xpath_field( this );
	} )
	/**
	 * show / hide input field for xpath rule
	 *
	 * @param tag field
	 */
	function advads_show_placement_content_xpath_field( tag_field ){
		// get the value of the content tag option
		var tag = $( tag_field ).find( 'option:selected').val();
		// show or hide the next following custom xpath option
		if( 'custom' === tag ) {
			$( tag_field ).next( '.advads-placements-content-custom-xpath' ).show();
		} else {
			$( tag_field ).next( '.advads-placements-content-custom-xpath' ).hide();
		}
	}

	/**
	 * Image ad uploader
	 */
	$( 'body' ).on( 'click', '.advads_image_upload', function ( e ) {

		e.preventDefault()

		var button = $( this )

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			// file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			file_frame.open()
			return
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media( {
			id: 'advads_type_image_wp_media',
			title: button.data( 'uploaderTitle' ),
			button: {
				text: button.data( 'uploaderButtonText' )
			},
			library: {
				type: 'image'
			},
			multiple: false // only allow one file to be selected
		} )

		// When an image is selected, run a callback.
		file_frame.on( 'select', function () {

			var selection = file_frame.state().get( 'selection' )
			selection.each( function ( attachment, index ) {
				attachment = attachment.toJSON()
				if ( 0 === index ) {
					// place first attachment in field
					$( '#advads-image-id' ).val( attachment.id )
					$( '#advanced-ads-ad-parameters-size input[name="advanced_ad[width]"]' ).val( attachment.width )
					$( '#advanced-ads-ad-parameters-size input[name="advanced_ad[height]"]' ).val( attachment.height )
					// update image preview
					var new_image = '<img width="' + attachment.width + '" height="' + attachment.height +
						'" title="' + attachment.title + '" alt="' + attachment.alt + '" src="' + attachment.url + '"/>'
					$( '#advads-image-preview' ).html( new_image )
					$( '#advads-image-edit-link' ).attr( 'href', attachment.editLink )
					// process "reserve this space" checkbox
					$( '#advanced-ads-ad-parameters-size input[type=number]:first' ).trigger( 'change' );
				}
			} )
		} )

		// Finally, open the modal
		file_frame.open()
	} )

	// WP 3.5+ uploader
	var file_frame
	window.formfield = ''

	// adblocker related code
	$( '#advanced-ads-use-adblocker' ).on( 'change', function () {
		advads_toggle_box( this, '#advads-adblocker-wrapper' )
	} )

	// processing of the rebuild asset form and the FTP/SSH credentials form
	var $advads_adblocker_wrapper = $( '#advads-adblocker-wrapper' )
	$advads_adblocker_wrapper.find( 'input[type="submit"]' ).prop( 'disabled', false )
	$advads_adblocker_wrapper.on( 'submit', 'form', function ( event ) {
		event.preventDefault()
		var $form = $( '#advanced-ads-rebuild-assets-form' )
		$form.prev( '.error' ).remove()
		$form.find( 'input[type="submit"]' ).prop( 'disabled', true ).after( '<span class="spinner advads-spinner"></span>' )

		var args = {
			data: {
				action: 'advads-adblock-rebuild-assets',
				nonce: advadsglobal.ajax_nonce,
			},
			done: function ( data, textStatus, jqXHR ) {
				var $advads_adblocker_wrapper = $( '#advads-adblocker-wrapper' )
				$advads_adblocker_wrapper.html( data )
			},
			fail: function ( jqXHR, textStatus, errorThrown ) {
				$form.before( '<div class="error"><p>' + textStatus + ': ' + errorThrown + '</p></div>' )
				$form.find( 'input[type="submit"]' ).prop( 'disabled', false ).next( '.advads-spinner' ).remove()
			},
			on_modal_close: function () {
				var $form = $( '#advanced-ads-rebuild-assets-form' )
				$form.find( 'input[type="submit"]' ).prop( 'disabled', false ).next( '.advads-spinner' ).remove()
			}
		}

		$.each( $form.serializeArray(), function ( i, o ) {
			args.data[ o.name ] = o.value
		} )

		advanced_ads_admin.filesystem.ajax( args )
	} )

	// process "reserve this space" checkbox
	$( '#advanced-ads-ad-parameters' ).on( 'change', '#advanced-ads-ad-parameters-size input[type=number]', function () {
		// Check if width and/or height is set.
		if ( $( '#advanced-ads-ad-parameters-size input[type=number]' ).filter( function () {
			return parseInt( this.value, 10 ) > 0
		} ).length >= 1 ) {
			$( '#advads-wrapper-add-sizes' ).prop( 'disabled', false )
		} else {
			$( '#advads-wrapper-add-sizes' ).prop( 'disabled', true ).prop( 'checked', false )
		}
	} )
	// process "reserve this space" checkbox - ad type changed
	$( '#advanced-ads-ad-parameters' ).on( 'paramloaded', function () {
		$( '#advanced-ads-ad-parameters-size input[type=number]:first' ).trigger( 'change' );
	} );
	// process "reserve this space" checkbox - on load
	$( '#advanced-ads-ad-parameters-size input[type=number]:first' ).trigger( 'change' );

	// move meta box markup to hndle headline
	$( '.advads-hndlelinks' ).each( function () {
		$( this ).appendTo( $( this ).parents('.postbox').find( 'h2.hndle' ) )
		$( this ).removeClass( 'hidden' )
	} );
	// open tutorial link when clicked on it
	$( '.advads-video-link' ).on( 'click', function ( el ) {
		el.preventDefault()
		var video_container = $( this ).parents( 'h2' ).siblings( '.inside' ).find( '.advads-video-link-container' )
		video_container.html( video_container.data( 'videolink' ) )
	} );
	// open inline tutorial link when clicked on it
	$( '.advads-video-link-inline' ).on( 'click', function ( el ) {
		el.preventDefault()
		var video_container = $( this ).parents( 'div' ).siblings( '.advads-video-link-container' )
		video_container.html( video_container.data( 'videolink' ) )
	} );
	// switch import type
	jQuery( '.advads_import_type' ).on( 'change', function () {
		if ( this.value === 'xml_content' ) {
			jQuery( '#advads_xml_file' ).hide()
			jQuery( '#advads_xml_content' ).show()
		} else {
			jQuery( '#advads_xml_file' ).show()
			jQuery( '#advads_xml_content' ).hide()
		}
	} );

	// Find Adsense Auto Ads inside ad content.
	var ad_content = jQuery( 'textarea[name=advanced_ad\\[content\\]]' ).html()
	if (
		(ad_content && ad_content.indexOf('enable_page_level_ads') !== -1)
		|| /script[^>]+data-ad-client=/.test(ad_content)
	) {
		advads_show_adsense_auto_ads_warning()
	}

	//advads_ads_txt_check_third_party();
	advads_ads_txt_find_issues()

	jQuery( '.advanced-ads-adsense-dashboard' ).each( function ( key, elm ) {
		Advanced_Ads_Adsense_Helper.process_dashboard( elm )
	} )
} )

/**
 * Store the action hash in settings form action
 * thanks for Yoast SEO for this idea
 */
function advads_set_tab_hashes () {
	// iterate through forms
	jQuery( '#advads-tabs' ).find( 'a' ).each( function () {
		var id        = jQuery( this ).attr( 'id' ).replace( '-tab', '' )
		var optiontab = jQuery( '#' + id )

		var form = optiontab.children( '.advads-settings-tab-main-form' )
		if ( form.length ) {
			var currentUrl = form.attr( 'action' ).split( '#' )[ 0 ]
			form.attr( 'action', currentUrl + jQuery( this ).attr( 'href' ) )
		}
	} )
}

/**
 * Scroll to position in backend minus admin bar height
 *
 * @param selector jQuery selector
 */
function advads_scroll_to_element ( selector ) {
	var height_of_admin_bar = jQuery( '#wpadminbar' ).height()
	jQuery( 'html, body' ).animate( {
		scrollTop: jQuery( selector ).offset().top - height_of_admin_bar
	}, 1000 )
}

/**
 * toggle content elements (hide/show)
 *
 * @param selector jquery selector
 */
function advads_toggle ( selector ) {
	jQuery( selector ).slideToggle()
}

/**
 * toggle content elements with a checkbox (hide/show)
 *
 * @param selector jquery selector
 */
function advads_toggle_box ( e, selector ) {
	if ( jQuery( e ).is( ':checked' ) ) {
		jQuery( selector ).slideDown()
	} else {
		jQuery( selector ).slideUp()
	}
}

/**
 * disable content of one box when selecting another
 *  only grey/disable it, don’t hide it
 *
 * @param selector jquery selector
 */
function advads_toggle_box_enable ( e, selector ) {
	if ( jQuery( e ).is( ':checked' ) ) {
		jQuery( selector ).find( 'input' ).removeAttr( 'disabled', '' )
	} else {
		jQuery( selector ).find( 'input' ).attr( 'disabled', 'disabled' )
	}
}

/**
 * validate placement form on submit
 */
function advads_validate_placement_form () {
	// check if placement type was selected
	if ( ! jQuery( '.advads-placement-type input:checked' ).length ) {
		jQuery( '.advads-placement-type-error' ).show()
		return false
	} else {
		jQuery( '.advads-placement-type-error' ).hide()
	}
	// check if placement name was entered
	if ( jQuery( '.advads-new-placement-name' ).val() == '' ) {
		jQuery( '.advads-placement-name-error' ).show()
		return false
	} else {
		jQuery( '.advads-placement-name-error' ).hide()
	}
	return true
}

/**
 * replace textarea with TinyMCE editor for Rich Content ad type
 */
function advads_maybe_textarea_to_tinymce ( ad_type ) {
	var textarea            = jQuery( '#advads-ad-content-plain' ),
			textarea_html       = textarea.val(),
			tinymce_id          = 'advanced-ads-tinymce',
			tinymce_id_ws       = jQuery( '#' + tinymce_id ),
			tinymce_wrapper_div = jQuery( '#advanced-ads-tinymce-wrapper' )

	if ( ad_type !== 'content' ) {
		tinymce_id_ws.prop( 'name', tinymce_id )
		tinymce_wrapper_div.hide()
		return false
	}

	if ( typeof tinyMCE === 'object' && tinyMCE.get( tinymce_id ) !== null ) {
		// visual mode
		if ( textarea_html ) {
			// see BeforeSetContent in the wp-includes\js\tinymce\plugins\wordpress\plugin.js
			var wp         = window.wp,
					hasWpautop = (wp && wp.editor && wp.editor.autop && tinyMCE.get( tinymce_id ).getParam( 'wpautop', true ))
			if ( hasWpautop ) {
				textarea_html = wp.editor.autop( textarea_html )
			}
			tinyMCE.get( tinymce_id ).setContent( textarea_html )
		}
		textarea.remove()
		tinymce_id_ws.prop( 'name', textarea.prop( 'name' ) )
		tinymce_wrapper_div.show()
	} else if ( tinymce_id_ws.length ) {
		// text mode
		tinymce_id_ws.val( textarea_html )
		textarea.remove()
		tinymce_id_ws.prop( 'name', textarea.prop( 'name' ) )
		tinymce_wrapper_div.show()
	}
}

/**
 * Show a message depending on whether Adsense Auto ads are enabled.
 */
function advads_show_adsense_auto_ads_warning () {
	$msg = jQuery( '.advads-auto-ad-in-ad-content' ).show()
	$msg.on( 'click', 'button', function () {
		$msg.hide()
		jQuery.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'advads-adsense-enable-pla',
				nonce: advadsglobal.ajax_nonce
			},
		} ).done( function ( data ) {
			$msg.show().html( advadstxt.page_level_ads_enabled )
		} ).fail( function ( jqXHR, textStatus ) {
			$msg.show()
		} )
	} )
}

/**
 * Check if a third-party ads.txt file exists.
 */
function advads_ads_txt_find_issues () {
	var $wrapper = jQuery( '#advads-ads-txt-notice-wrapper' )
	var $refresh = jQuery( '#advads-ads-txt-notice-refresh' )
	var $actions = jQuery( '.advads-ads-txt-action' )

	/**
	 * Toggle the visibility of the spinner.
	 *
	 * @param {Bool} state True to show, False to hide.
	 */
	function set_loading ( state ) {
		$actions.toggle( ! state )
		if ( state ) {
			$wrapper.html( '<span class="spinner advads-spinner"></span>' )
		}
	}

	if ( ! $wrapper.length ) {
		return
	}

	if ( ! $wrapper.find( 'ul' ).length ) {
		// There are no notices. Fetch them using ajax.
		load( 'get_notices' )
	}

	$refresh.on('click', function () {
		load( 'get_notices' )
	} )

	function done ( response ) {
		$wrapper.html( response.notices )
		set_loading( false )
	}

	function fail ( jqXHR ) {
		$wrapper.html( '<p class="advads-error-message">'
			+ jQuery( '#advads-ads-txt-notice-error' ).text().replace( '%s', parseInt( jqXHR.status, 10 ) ),
			+'</p>'
		)
		set_loading( false )
	}

	function load ( type ) {
		set_loading( true )

		jQuery.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'advads-ads-txt',
				nonce: advadsglobal.ajax_nonce,
				type,
			},
		} ).done( done ).fail( fail )
	}

	jQuery( document ).on( 'click', '#advads-ads-txt-remove-real', function ( event ) {
		event.preventDefault()

		var args = {
			data: {
				action: 'advads-ads-txt',
				nonce: advadsglobal.ajax_nonce,
				type: 'remove_real_file',
			},
			done: function ( response ) {
				if ( response.additional_content ) {
					jQuery( '#advads-ads-txt-additional-content' ).val( response.additional_content )
				}
				done( response )
			},
			fail: fail,
			before_send: function () {
				set_loading( true )
			},
		}

		advanced_ads_admin.filesystem.ajax( args )
	} )

	jQuery( document ).on( 'click', '#advads-ads-txt-create-real', function ( event ) {
		event.preventDefault()

		var args = {
			data: {
				action: 'advads-ads-txt',
				nonce: advadsglobal.ajax_nonce,
				type: 'create_real_file',
			},
			done: done,
			fail: fail,
			before_send: function () {
				set_loading( true )
			},
		}

		advanced_ads_admin.filesystem.ajax( args )
	} )

}


window.advanced_ads_admin     = window.advanced_ads_admin || {}
advanced_ads_admin.filesystem = {
	/**
	 * Holds the current job while the user writes data in the 'Connection Information' modal.
	 *
	 * @type {obj}
	 */
	_locked_job: false,

	/**
	 * Toggle the 'Connection Information' modal.
	 */
	_requestForCredentialsModalToggle: function () {
		this.$filesystemModal.toggle()
		jQuery( 'body' ).toggleClass( 'modal-open' )
	},

	_init: function () {
		this._init = function () {}
		var self   = this

		self.$filesystemModal = jQuery( '#advanced-ads-rfc-dialog' )
		/**
		 * Sends saved job.
		 */
		self.$filesystemModal.on( 'submit', 'form', function ( event ) {
			event.preventDefault()

			self.ajax( self._locked_job, true )
			self._requestForCredentialsModalToggle()
		} )

		/**
		 * Closes the request credentials modal when clicking the 'Cancel' button.
		 */
		self.$filesystemModal.on( 'click', '[data-js-action="close"]', function () {
			if ( jQuery.isPlainObject( self._locked_job ) && self._locked_job.on_modal_close ) {
				self._locked_job.on_modal_close()
			}

			self._locked_job = false
			self._requestForCredentialsModalToggle()
		} )
	},

	/**
	 * Sends AJAX request. Shows 'Connection Information' modal if needed.
	 *
	 * @param {object} args
	 * @param {bool} skip_modal
	 */
	ajax: function ( args, skip_modal ) {
		this._init()

		if ( ! skip_modal && this.$filesystemModal.length > 0 ) {
			this._requestForCredentialsModalToggle()
			this.$filesystemModal.find( 'input:enabled:first' ).focus()

			// Do not send request.
			this._locked_job = args
			return
		}

		var options = {
			method: 'POST',
			url: window.ajaxurl,
			data: {
				username: jQuery( '#username' ).val(),
				password: jQuery( '#password' ).val(),
				hostname: jQuery( '#hostname' ).val(),
				connection_type: jQuery( 'input[name="connection_type"]:checked' ).val(),
				public_key: jQuery( '#public_key' ).val(),
				private_key: jQuery( '#private_key' ).val(),
				_fs_nonce: jQuery( '#_fs_nonce' ).val()

			}
		}

		if ( args.before_send ) {
			args.before_send()
		}

		options.data = jQuery.extend( options.data, args.data )
		var request  = jQuery.ajax( options )

		if ( args.done ) {
			request.done( args.done )
		}

		if ( args.fail ) {
			request.fail( args.fail )
		}
	}
}

window.Advanced_Ads_Admin = window.Advanced_Ads_Admin || {
	init_ad_source_editor: function () {

	},
	get_ad_source_editor_text: function () {
		var text = undefined
		if ( Advanced_Ads_Admin.editor ) {
			if ( Advanced_Ads_Admin.editor.codemirror ) {
				text = Advanced_Ads_Admin.editor.codemirror.getValue()
			}
		}
		if ( ! text ) {
			var ta = jQuery( '#advads-ad-content-plain' )
			if ( ta ) text = ta.val()
		}
		return text
	},
	set_ad_source_editor_text: function ( text ) {
		if ( Advanced_Ads_Admin.editor && Advanced_Ads_Admin.editor.codemirror ) {
			Advanced_Ads_Admin.editor.codemirror.setValue( text )
		} else {
			jQuery( '#advads-ad-content-plain' ).val( text )
		}
	},
	check_ad_source: function () {
		var text        = Advanced_Ads_Admin.get_ad_source_editor_text()
		var enabled_php = jQuery( '#advads-parameters-php' ).prop( 'checked' )
		var enabled_sc  = jQuery( '#advads-parameters-shortcodes' ).prop( 'checked' )
		if ( enabled_php && ! /\<\?php/.test( text ) ) {
			jQuery( '#advads-parameters-php-warning' ).show()
		} else {
			jQuery( '#advads-parameters-php-warning' ).hide()
		}
		if ( enabled_sc && ! /\[[^\]]+\]/.test( text ) ) {
			jQuery( '#advads-parameters-shortcodes-warning' ).show()
		} else {
			jQuery( '#advads-parameters-shortcodes-warning' ).hide()
		}
	},

	/**
	 * Toggle placement advanced options.
	 *
	 * @deprecated. Used only by add-ons when the base plugin version < 1.19.
	 */
	toggle_placements_visibility: function ( elm, state ) {
		var advadsplacementformrow = jQuery( elm ).next( '.advads-placements-advanced-options' )

		var hide = ( typeof state !== 'undefined' ) ? ! state : advadsplacementformrow.is( ':visible' );;
		if ( hide ) {
			advadsplacementformrow.hide()
			// clear last edited id
			jQuery( '#advads-last-edited-placement' ).val( '' )
		} else {
			var placement_id = jQuery( elm ).parents( '.advads-placements-table-options' ).find( '.advads-placement-slug' ).val()
			advadsplacementformrow.show()
			jQuery( '#advads-last-edited-placement' ).val( placement_id )
			// Some special elements (color picker) may not be detected with jquery.
			var tr = jQuery( elm ).closest( 'tr.advanced-ads-placement-row' )
			if ( tr ) {
				tr.data( 'touched', true )
			}
		}
	},

	/**
	 * get a cookie value
	 *
	 * @param {str} name of the cookie
	 */
	get_cookie: function (name) {
		var i, x, y, ADVcookies = document.cookie.split( ";" );
		for (i = 0; i < ADVcookies.length; i++)
		{
			x = ADVcookies[i].substr( 0, ADVcookies[i].indexOf( "=" ) );
			y = ADVcookies[i].substr( ADVcookies[i].indexOf( "=" ) + 1 );
			x = x.replace( /^\s+|\s+$/g, "" );
			if (x === name)
			{
				return unescape( y );
			}
		}
	},

	/**
	 * set a cookie value
	 *
	 * @param {str} name of the cookie
	 * @param {str} value of the cookie
	 * @param {int} exdays days until cookie expires
	 *  set 0 to expire cookie immidiatelly
	 *  set null to expire cookie in the current session
	 */
	set_cookie: function (name, value, exdays, path, domain, secure) {
		// days in seconds
		var expiry = ( exdays == null ) ? null : exdays * 24 * 60 * 60;
		this.set_cookie_sec( name, value, expiry, path, domain, secure );
	},
	/**
	 * set a cookie with expiry given in seconds
	 *
	 * @param {str} name of the cookie
	 * @param {str} value of the cookie
	 * @param {int} expiry seconds until cookie expires
	 *  set 0 to expire cookie immidiatelly
	 *  set null to expire cookie in the current session
	 */
	set_cookie_sec: function (name, value, expiry, path, domain, secure) {
		var exdate = new Date();
		exdate.setSeconds( exdate.getSeconds() + parseInt( expiry ) );
		document.cookie = name + "=" + escape( value ) +
			((expiry == null) ? "" : "; expires=" + exdate.toUTCString()) +
			((path == null) ? "; path=/" : "; path=" + path) +
			((domain == null) ? "" : "; domain=" + domain) +
			((secure == null) ? "" : "; secure");
	}
}

if ( ! window.AdvancedAdsAdmin ) window.AdvancedAdsAdmin = {}
if ( ! window.AdvancedAdsAdmin.AdImporter ) window.AdvancedAdsAdmin.AdImporter = {
	/**
	 * will highlight the currently selected ads in the list
	 * @param hideInactive when true will hide inactive ad units
	 * @returns the selector of the selected row or false if no row was selected.
	 */
	highlightSelectedRowInExternalAdsList: function ( hideInactive ) {
		if ( typeof (hideInactive) == 'undefined' ) hideInactive = AdvancedAdsAdmin.AdImporter.adNetwork.hideIdle
		const tbody = jQuery( '#mapi-table-wrap tbody' )
		const btn   = jQuery( '#mapi-toggle-idle' )

		//  count the ad units to determine if there's a need for the overflow class (scrolling)
		const nbUnits = hideInactive
			? jQuery( '#mapi-table-wrap tbody tr[data-active=1]' ).length
			: jQuery( '#mapi-table-wrap tbody tr' ).length
		if ( nbUnits > 8 ) jQuery( '#mapi-table-wrap' ).addClass( 'overflow' )
		else jQuery( '#mapi-table-wrap' ).removeClass( 'overflow' )

		//  hide inactive ads, but always show the selected one (if any)
		if ( hideInactive ) {
			btn.removeClass( 'dashicons-hidden' )
			btn.addClass( 'dashicons-visibility' )
			btn.attr( 'title', advadstxt.show_inactive_ads )
			tbody.find( 'tr[data-slotid]' ).each( function ( k, v ) {
				v = jQuery( v )
				if ( v.data( 'active' ) ) v.show()
				else v.hide()
			} )
		} else {
			btn.removeClass( 'dashicons-visibility' )
			btn.addClass( 'dashicons-hidden' )
			btn.attr( 'title', advadstxt.hide_inactive_ads )
			tbody.find( 'tr[data-slotid]' ).show()
		}

		const selectedRow = AdvancedAdsAdmin.AdImporter.getSelectedRow()
		tbody.find( 'tr' ).removeClass( 'selected error' );
		if ( selectedRow ) {
			//make sure, it is visible before applying the zebra stripes
			selectedRow.show()
		}

		//  make the table's rows striped.
		const visible = tbody.find( 'tr:visible' )
		visible.filter( ':odd' ).css( 'background-color', '#FFFFFF' )
		visible.filter( ':even' ).css( 'background-color', '#F9F9F9' )

		//  highlight the selected row
		if ( selectedRow ) {
			//  highlight the selected row
			selectedRow.css( 'background-color', '' )
			selectedRow.addClass( 'selected' )

			this.scrollToSelectedRow(selectedRow);
		}

		return selectedRow || false
	},

	scrollToSelectedRow($selectedRow) {
		const $wrap = jQuery('#mapi-table-wrap'),
			wrapHeight = $wrap.height(),
			wrapScrolled = $wrap.scrollTop();

		// just in case this does not get passed a selected row, scroll to top of the table
		if (!$selectedRow) {
			$wrap.animate({scrollTop: 0}, 200);
			return;
		}

		// get the position of the selectedRow within the table wrap
		let scroll = $selectedRow.position().top,
			bottom = +scroll + $selectedRow.height();
		// if the (top of the) element is not yet visible scroll to it
		if (scroll < wrapScrolled || bottom > wrapScrolled || scroll > wrapScrolled + wrapHeight) {
			// scrolled element is below current scroll position, i.e. we need to scroll past it not to top
			if (bottom > $wrap.children('table').height() - wrapHeight) {
				scroll = bottom;
			}

			// if the selected element is on the "first page" let's scroll all the way to the top
			if (scroll < wrapHeight) {
				scroll = 0;
			}

			$wrap.animate({scrollTop: scroll}, 200);
		}
	},

	getSelectedRow () {
		const selectedId = AdvancedAdsAdmin.AdImporter.adNetwork.getSelectedId()
		const tbody      = jQuery( '#mapi-table-wrap tbody' )

		if ( selectedId ) {
			const selectedRows = tbody.find( 'tr[data-slotid="' + selectedId + '"]' )
			if ( selectedRows.length ) {
				return selectedRows
			}
		}
		return null
	},
	openExternalAdsList: function () {
		const network = AdvancedAdsAdmin.AdImporter.adNetwork
		network.openSelector()

		jQuery( '.mapi-insert-code' ).css( 'display', 'inline' )
		jQuery( '.mapi-open-selector' ).css( 'display', 'none' )
		jQuery( '.mapi-close-selector-link' ).css( 'display', 'inline' )
		jQuery( '.advads-adsense-code' ).css( 'display', 'none' )
		jQuery( '#remote-ad-unsupported-ad-type' ).css( 'display', 'none' )

		AdvancedAdsAdmin.AdImporter.highlightSelectedRowInExternalAdsList( network.hideIdle )

		var SNT = network.getCustomInputs()
		SNT.css( 'display', 'none' )

		jQuery( '#mapi-wrap' ).css( 'display', 'block' )

		if ( ! network.fetchedExternalAds ) {
			network.fetchedExternalAds = true
			const nbUnits              = jQuery( '#mapi-table-wrap tbody tr[data-slotid]' ).length
			if ( nbUnits == 0 ) {
				//usually we start with a preloaded list.
				//only reload, when the count is zero (true for new accounts).
				AdvancedAdsAdmin.AdImporter.refreshAds()
			}
		}
		jQuery( '#wpwrap' ).trigger( 'advads-mapi-adlist-opened' );
		AdvancedAdsAdmin.AdImporter.resizeAdListHeader()
	},
	/**
	 * will be called every time the ad type is changed.
	 * required for onBlur detection
	 */
	onChangedAdType: function () {
		if ( AdvancedAdsAdmin.AdImporter.adNetwork ) {
			AdvancedAdsAdmin.AdImporter.adNetwork.onBlur()
			AdvancedAdsAdmin.AdImporter.adNetwork = null
		}
	},
	setup: function ( adNetwork ) {
		AdvancedAdsAdmin.AdImporter.adNetwork = adNetwork
		adNetwork.onSelected()
		if ( AdvancedAdsAdmin.AdImporter.isSetup ) {
			AdvancedAdsAdmin.AdImporter.highlightSelectedRowInExternalAdsList()
			return
		}
		AdvancedAdsAdmin.AdImporter.isSetup = true

		jQuery( document ).on( 'click', '.prevent-default', function ( ev ) { ev.preventDefault() } )

		//  handle clicks for the "insert new ... code" anchor
		jQuery( document ).on( 'click', '.mapi-insert-code', function ( e ) {
			e.preventDefault()
			jQuery( '#remote-ad-unsupported-ad-type' ).css( 'display', 'none' );
			jQuery( '.advads-adsense-code' ).show()
			jQuery( '.mapi-open-selector' ).css( 'display', 'inline' )
			jQuery( '.mapi-close-selector-link' ).css( 'display', 'inline' )
			jQuery( '.mapi-insert-code' ).css( 'display', 'none' );
			jQuery( '#mapi-wrap' ).css( 'display', 'none' )
			var SNT = AdvancedAdsAdmin.AdImporter.adNetwork.getCustomInputs()
			SNT.css( 'display', 'none' )
		} )

		//  handle clicks for the "get ad code from your linked account" anchor
		jQuery( document ).on( 'click', '.mapi-open-selector a', function () {
			AdvancedAdsAdmin.AdImporter.openExternalAdsList()
		} )

		//  the close button of the ad unit list
		jQuery( document ).on( 'click', '#mapi-close-selector,.mapi-close-selector-link', function () {
			jQuery( '#remote-ad-unsupported-ad-type' ).css( 'display', 'none' );
			AdvancedAdsAdmin.AdImporter.manualSetup()
		} )

		//the individual rows of the ad units may contain elements with the mapiaction class
		jQuery( document ).on( 'click', '.mapiaction', function ( ev ) {
			var action = jQuery( this ).attr( 'data-mapiaction' )
			switch ( action ) {
				case 'updateList':
					AdvancedAdsAdmin.AdImporter.refreshAds()
					break
				case 'getCode':
					if ( jQuery( this ).hasClass( 'disabled' ) ) {
						break
					}
					var slotId = jQuery( this ).attr( 'data-slotid' )
					AdvancedAdsAdmin.AdImporter.adNetwork.selectAdFromList( slotId )
					break
				case 'updateCode':
					var slotId = jQuery( this ).attr( 'data-slotid' )
					AdvancedAdsAdmin.AdImporter.adNetwork.updateAdFromList( slotId )
					break
				case 'toggleidle':
					if ( 'undefined' != typeof AdvancedAdsAdmin.AdImporter.adNetwork.getMapiAction && 'function' == typeof AdvancedAdsAdmin.AdImporter.adNetwork.getMapiAction( 'toggleidle' ) ) {
						AdvancedAdsAdmin.AdImporter.adNetwork.getMapiAction( 'toggleidle' )( ev, this );
					} else {
						AdvancedAdsAdmin.AdImporter.adNetwork.hideIdle = ! AdvancedAdsAdmin.AdImporter.adNetwork.hideIdle
						AdvancedAdsAdmin.AdImporter.toggleIdleAds( AdvancedAdsAdmin.AdImporter.adNetwork.hideIdle )
						const $inactiveNotice = jQuery( '#mapi-notice-inactive' );
						if ( $inactiveNotice.length ) {
							$inactiveNotice.toggle( AdvancedAdsAdmin.AdImporter.adNetwork.hideIdle );
						}
						break;
					}
				default:
			}
		} )

		AdvancedAdsAdmin.AdImporter.adNetwork.onDomReady()
		// AdvancedAdsAdmin.AdImporter.openExternalAdsList();

	},

	/**
	 * call this method to display the manual setup (if available for the current ad network)
	 * this method is an equivalent to the close ad list button.
	 */
	manualSetup () {
		jQuery( '.mapi-open-selector,.advads-adsense-show-code' ).css( 'display', 'inline' )
		jQuery( '.mapi-insert-code' ).css( 'display', 'inline' )
		jQuery( '.mapi-close-selector-link' ).css( 'display', 'none' )
		jQuery( '#mapi-wrap' ).css( 'display', 'none' )

		var SNT = AdvancedAdsAdmin.AdImporter.adNetwork.getCustomInputs()
		SNT.css( 'display', 'block' )
		// hide custom layout key if type is not in-feed
		if (jQuery('#unit-type').val() !== 'in-feed') {
			jQuery('.advads-adsense-layout-key').css('display', 'none')
				.next('div').css('display', 'none');
		}
		AdvancedAdsAdmin.AdImporter.adNetwork.onManualSetup()
	},

	setRemoteErrorMessage ( msg ) {
		if ( ! msg ) jQuery( '#remote-ad-code-msg' ).empty()
		else jQuery( '#remote-ad-code-msg' ).html( msg )
	},

	// another legacy method
	resizeAdListHeader: function () {
		var th = jQuery( '#mapi-list-header span' )
		var tb = jQuery( '#mapi-table-wrap tbody tr:visible' )
		var w  = []

		tb.first().find( 'td' ).each( function () {
			w.push( jQuery( this ).width() )
		} )

		th.each( function ( i ) {
			if ( i != w.length - 1 ) {
				jQuery( this ).width( w[ i ] )
			}
		} )
	},

	/**
	 * legacy method
	 */
	closeAdSelector: function () {
		// close the ad unit selector
		setTimeout( function () {
			jQuery( '#mapi-wrap' ).animate(
				{ height: 0, },
				360,
				function () {
					jQuery( '.mapi-open-selector,.advads-adsense-show-code' ).css( 'display', 'inline' )
					jQuery( '.mapi-close-selector-link' ).css( 'display', 'none' )
					jQuery( '#mapi-wrap' ).css( {
						display: 'none',
						height: 'auto',
					} )
					const SNT = AdvancedAdsAdmin.AdImporter.adNetwork.getCustomInputs()
					SNT.css( 'display', 'block' )
				}
			)
		}, 80 )

	},

	/**
	 * legacy method
	 * updates the UI, (call if the selected unit is supported)
	 */
	unitIsNotSupported: function ( slotID ) {
		jQuery( '#remote-ad-unsupported-ad-type' ).css( 'display', 'block' );
		AdsenseMAPI.unsupportedUnits[ slotID ] = 1;
		jQuery( '#unit-code' ).val( '' );
		jQuery( '#unit-type' ).val( 'normal' );
		jQuery( '#ad-layout-key' ).val( '' );
		jQuery( 'tr[data-slotid^="ca-"]' ).removeClass( 'selected error' );
		var $selectedRow = jQuery('tr[data-slotid="' + slotID + '"]');
		$selectedRow.addClass('selected error').css('background-color', '');
		this.scrollToSelectedRow($selectedRow);
	},

	/**
	 * legacy method
	 * updates the UI, (call if the selected unit is NOT supported)
	 */
	unitIsSupported: function ( slotID ) {
		jQuery( '#remote-ad-unsupported-ad-type' ).css( 'display', 'none' )
		if ( 'undefined' != typeof AdsenseMAPI.unsupportedUnits[ slotID ] ) {
			delete AdsenseMAPI.unsupportedUnits[ slotID ]
		}
		jQuery( 'i[data-mapiaction="getCode"][data-slotid="' + slotID + '"]' ).removeClass( 'disabled' )
		if ( jQuery( 'tr[data-slotid="' + slotID + '"] .unittype a' ).length ) {
			var td      = jQuery( 'tr[data-slotid="' + slotID + '"] .unittype' )
			var content = jQuery( 'tr[data-slotid="' + slotID + '"] .unittype a' ).attr( 'data-type' )
			td.text( content )
		}
		if ( jQuery( 'tr[data-slotid="' + slotID + '"] .unitsize a' ).length ) {
			var td      = jQuery( 'tr[data-slotid="' + slotID + '"] .unitsize' )
			var content = jQuery( 'tr[data-slotid="' + slotID + '"] .unitsize a' ).attr( 'data-size' )
			td.text( content )
		}
	},

	/**
	 * legacy method
	 * updates the UI, (call if the selected unit is NOT supported)
	 */
	emptyMapiSelector: function ( msg ) {
		const nag = '<div class="notice notice-error" style="font-size:1.1em;padding:.6em 1em;font-weight:bold;">' + msg + '</div>'
		jQuery( '#mapi-loading-overlay' ).css( 'display', 'none' )
		jQuery( '#mapi-wrap' ).html( jQuery( nag ) )
	},

	/**
	 * legacy method
	 */
	refreshAds: function () {
		const adNetwork = AdvancedAdsAdmin.AdImporter.adNetwork
		jQuery( '#mapi-loading-overlay' ).css( 'display', 'block' );
		jQuery.ajax( {
			type: 'post',
			url: ajaxurl,
			data: adNetwork.getRefreshAdsParameters(),
			success: function ( response, status, XHR ) {
				if ( 'undefined' != typeof response.html ) {
					jQuery( '#mapi-wrap' ).replaceWith( jQuery( response.html ) )
					AdvancedAdsAdmin.AdImporter.openExternalAdsList()
				} else if ( 'undefined' != typeof response.msg ) {
					AdvancedAdsAdmin.AdImporter.emptyMapiSelector( response.msg )
				}
				if ( 'undefined' != typeof response.raw ) {
					console.log( response.raw )
				}
				jQuery( '#mapi-loading-overlay' ).css( 'display', 'none' )
			},
			error: function ( request, status, err ) {
				jQuery( '#mapi-loading-overlay' ).css( 'display', 'none' )
			},
		} )

	},

	toggleIdleAds: function ( hide ) {
		if ( 'undefined' == typeof hide ) {
			hide = true
		}
		AdvancedAdsAdmin.AdImporter.highlightSelectedRowInExternalAdsList( hide )
	}
}

/**
 * The "abstract" base class for handling external ad units
 * Every ad unit will provide you with a set of methods to control the GUI and trigger requests to the server
 * while editing an ad that is backed by this network. The main logic takes place in admin/assets/admin.js,
 * and the methods in this class are the ones that needed abstraction, depending on the ad network. When you
 * need new network-dependant features in the frontend, this is the place to add new methods.
 *
 * An AdvancedAdsAdNetwork uses these fields:
 * id string The identifier, that is used for this network. Must match with the one used in the PHP code of Advanced Ads
 * units array Holds the ad units of this network.
 * vars map These are the variables that were transmitted from the underlying PHP class (method: append_javascript_data)
 * hideIdle Remembers, wheter idle ads should be displayed in the list;
 * fetchedExternalAds Remembers if the external ads list has already been loaded to prevent unneccesary requests
 */
class AdvancedAdsAdNetwork {
	/**
	 * @param id string representing the id of this network. has to match the identifier of the PHP class
	 */
	constructor ( id ) {
		this.id                 = id
		this.units              = []
		this.vars               = window[ id + 'AdvancedAdsJS' ]
		this.hideIdle           = true
		this.fetchedExternalAds = false
	}

	/**
	 * will be called when an ad network is selected (ad type in edit ad)
	 */
	onSelected () {
		console.error( 'Please override onSelected.' )
	}

	/**
	 * will be called when an ad network deselected (ad type in edit ad)
	 */
	onBlur () {
		console.error( 'Please override onBlur.' )
	}

	/**
	 * opens the selector list containing the external ad units
	 */
	openSelector () {
		console.error( 'Please override openSelector.' )
	}

	/**
	 * returns the network specific id of the currently selected ad unit
	 */
	getSelectedId () {
		console.error( 'Please override getSelectedId.' )
	}

	/**
	 * will be called when an external ad unit has been selected from the selector list
	 * @param slotId string the external ad unit id
	 */
	selectAdFromList ( slotId ) {
		console.error( 'Please override selectAdFromList.' )
	}

	/**
	 * will be called when an the update button of an external ad unit has been clicked
	 * TODO: decide wheter to remove this method. not required anymore - the button was removed.
	 * @param slotId string the external ad unit id
	 */
	updateAdFromList ( slotId ) {
		console.error( 'Please override updateAdFromList.' )
	}

	/**
	 * return the POST params that you want to send to the server when requesting a refresh of the external ad units
	 * (like nonce and action and everything else that is required)
	 */
	getRefreshAdsParameters () {
		console.error( 'Please override getRefreshAdsParameters.' )
	}

	/**
	 * return the jquery objects for all the custom html elements of this ad type
	 */
	getCustomInputs () {
		console.error( 'Please override getCustomInputs.' )
	}

	/**
	 * what to do when the DOM is ready
	 */
	onDomReady () {
		console.error( 'Please override onDomReady.' )
	}

	/**
	 * when you need custom behaviour for ad networks that support manual setup of ad units, override this method
	 */
	onManualSetup () {
		//no console logging. this is optional
	}
}

class AdvancedAdsExternalAdUnit {

}

/**
 * todo: this looks like something we could use in general, but where to put it?
 */
jQuery( document ).ready( function () {
	// delete an existing row by removing the parent tr tag
	// todo: this could be moved to a general file
	jQuery( document ).on( 'click', '.advads-tr-remove', function(){
		jQuery( this ).closest( 'tr' ).remove();
	});
});
