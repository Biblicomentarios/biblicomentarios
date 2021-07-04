/**
 * Credentials
 *
 * @package ShareThisShareButtons
 */

/* exported Credentials */
var Credentials = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 *
		 * @param data
		 */
		boot: function( data ) {
			this.data = data;

			$( document ).ready( function() {
				this.init();
			}.bind( this ) );
		},

		/**
		 * Initialize plugin.
		 */
		init: function() {
			this.$connection = $( '.sharethis-connection-wrap' );
			this.$createConfig = '';
			this.listen();
			this.loadPreview( 'initial' );
			this.checkAdBlock();
		},

		/**
		 * Listener.
		 */
		listen: function() {
			var self = this;

			// Create new account.
			this.$connection.on( 'click', '.create-account', function() {
				var email = $( '#st-email' ).val(),
					pw = $( '#st-password' ).val();

				$( '.st-loading-gif' ).fadeIn();

				// Set default WP config.
				wp.ajax.post( 'set_default_settings', {
					type: 'both',
					nonce: self.data.nonce
				} ).always( function( link ) {
					self.registerAccount( email, pw );
				}.bind( self ) );
			} );

			// Login to account.
			this.$connection.on( 'click', '.login-account', function( e ) {
				e.preventDefault();

				var email = $( '#st-login-email' ).val(),
					pw = $( '#st-login-password' ).val();

				// Set default WP config.
				wp.ajax.post( 'set_default_settings', {
					type: 'both',
					nonce: self.data.nonce
				} ).always( function( link ) {
					self.loginAccount( email, pw );
				}.bind( self ) );
			} );

			this.$connection.on( 'click', '#connect-property', function( e ) {
				e.preventDefault();

				$( '.st-loading-gif' ).fadeIn();

				var secret = $( '#sharethis-properties option:selected' ).val(),
					property = $( '#sharethis-properties option:selected' ).attr( 'data-prop' ),
					token = $( '#st-user-cred' ).val(),
					config = $( '#sharethis-properties option:selected' ).attr( 'data-config' ).replace( /'/g, '"' ),
					button = $( '#sharethis-properties option:selected' ).attr( 'data-first' ).replace( '-share-buttons', '' ),
					theData = JSON.stringify( { is_wordpress: true } ),
					callExtra = 'secret=' + secret;

				if ( 'undefined' === secret ) {
					callExtra = 'token=' + token;
				}

				wp.ajax.post( 'set_button_config', {
					button: button,
					config: config,
					type: 'login',
					nonce: self.data.nonce
				} ).always( function() {
					$.ajax( {
						url: 'https://platform-api.sharethis.com/v1.0/property/?id=' + property + '&' + callExtra,
						method: 'PUT',
						async: false,
						contentType: 'application/json; charset=utf-8',
						data: theData,
						success: function() {
							self.setCredentials( secret, property, token, 'login' );
						}
					} );
				} );
			} );

			// Create property based on site url.
			this.$connection.on( 'click', '#create-new-property', function( e ) {
				e.preventDefault();

				$( '.st-loading-gif' ).fadeIn();

				var secret = $( '#sharethis-properties option:selected' ).val(),
					property = $( '#sharethis-properties option:selected' ).attr( 'data-prop' ),
					token = $( '#st-user-cred' ).val(),
					config = $( '#sharethis-properties option:selected' ).attr( 'data-config' ).replace( /'/g, '"' ),
					button = $( '#sharethis-properties option:selected' ).attr( 'data-first' ).replace( '-share-buttons', '' ),
					theData = JSON.stringify( { is_wordpress: true } ),
					callExtra = 'secret=' + secret;

				if ( 'undefined' === secret ) {
					callExtra = 'token=' + token;
				}

				wp.ajax.post( 'set_button_config', {
					button: button,
					config: config,
					type: 'login',
					nonce: self.data.nonce
				} ).always( function( results ) {
					$.ajax( {
						url: 'https://platform-api.sharethis.com/v1.0/property/?id=' + property + '&' + callExtra,
						method: 'PUT',
						async: false,
						contentType: 'application/json; charset=utf-8',
						data: theData,
						success: function() {
							self.$createConfig = JSON.parse( config );
							self.$createButton = button;
							self.createProperty( token, self.data.url, 'create' );
						}
					} );
				} );
			} );

			$( 'body' ).on( 'click', '.item label', function() {
				var checked = $( this ).siblings( 'input' ).is( ':checked' );

				$( '.sharethis-inline-share-buttons' ).removeClass( 'st-has-labels' );

				if ( ! checked ) {
					$( this ).closest( '.st-radio-config' ).find( '.item' ).each( function() {
						$( this ).find( 'input' ).prop( 'checked', false );
					} );

					$( this ).siblings( 'input' ).prop( 'checked', true );
				}

				self.loadPreview( '' );
			} );

			// All levers.
			this.$connection.on( 'click', '.item div.switch', function() {
				self.loadPreview( '' );
			} );

			// Minimum count.
			this.$connection.on( 'change', 'input.minimum-count, #radius-selector, .vertical-alignment, .mobile-breakpoint, #st-language', function() {
				self.loadPreview( '' );
			} );

			// Button alignment.
			this.$connection.on( 'click', '.button-alignment .alignment-button', function() {
				$( '.button-alignment .alignment-button[data-selected="true"]' )
					.attr( 'data-selected', 'false' );
				$( '.sharethis-inline-share-buttons' ).removeClass( 'st-justified' );
				$( this ).attr( 'data-selected', 'true' );

				self.loadPreview( '' );
			} );

			// Select or deselect a network.
			this.$connection.on( 'click', '.share-buttons .share-button', function() {
				var selection = $( this ).attr( 'data-selected' ),
					network = $( this ).attr( 'data-network' );

				if ( 'true' === selection ) {
					$( this ).attr( 'data-selected', 'false' );
					$( '.sharethis-selected-networks > div > div div[data-network="' + network + '"]' ).remove();
				} else {
					$( this ).attr( 'data-selected', 'true' );
					$( '.sharethis-selected-networks > div > div' ).append( '<div class="st-btn" data-network="' + network + '" style="display: inline-block;"></div>' );
				}

				self.loadPreview( '' );
			} );

			// Add class to preview when scrolled to.
			$( window ).on( 'scroll', function() {
				if ( undefined === $( '.selected-button' ).offset() ) {
					return;
				}

				var stickyTop = $( '.selected-button' ).offset().top;

				if ( $( window ).scrollTop() >= stickyTop ) {
					$( '.sharethis-selected-networks' ).addClass( 'sharethis-prev-stick' );
				} else {
					$( '.sharethis-selected-networks' ).removeClass( 'sharethis-prev-stick' );
				}
			} );

			// If register button is clicked. submit button configurations.
			this.$connection.on( 'click', '#sharethis-step-two-wrap .st-rc-link', function() {
				$( '.st-loading-gif' ).fadeIn();
				self.loadPreview( 'submit' );
			} );
		},

		/**
		 * Send hash data to credential setting.
		 *
		 * @param secret
		 * @param propertyid
		 * @param token
		 * @param type
		 */

		setCredentials: function( secret, propertyid, token, type ) {
			var propSecret = propertyid + '-' + secret;

			// If hash exists send it to credential setting.
			wp.ajax.post( 'set_credentials', {
				data: propSecret,
				token: token,
				nonce: this.data.nonce
			} ).always( function( link ) {
				if ( 'login' !== type ) {
					this.setButtonConfig( secret, propertyid, token, type );
				} else {
					window.location = '?page=sharethis-share-buttons';
				}
			}.bind( this ) );
		},

		/**
		 * Login to your account.
		 *
		 * @param email
		 * @param pw
		 */
		loginAccount: function( email, pw ) {
			var self = this,
				theData = JSON.stringify( {
					email: email,
					password: pw
				} );

			$.ajax( {
				url: 'https://sso.sharethis.com/login',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					$( '#st-user-cred' ).val( results.token );

					// Get full info.
					self.getProperty( results.token );
				},
				error: function( xhr, status, error ) {
					$( '.st-loading-gif' ).hide();
					var message = xhr.responseJSON.message;

					$( 'div.error-message' ).html( '' );
					$( '.login-account.st-rc-link' ).after(
						'<div class="error-message" style="text-align: center; margin: 1rem 0;">' +
						message +
						'</div>'
					);
				}
			} );
		},

		/**
		 * Register new account.
		 *
		 * @param email
		 * @param pw
		 */
		registerAccount: function( email, pw ) {
			var result = null,
				self = this,
				url = this.data.url,
				button = this.data.firstButton,
				theData = JSON.stringify( {
					email: email,
					password: pw,
					custom: {
						onboarding_product: button + '-share-buttons',
						onboarding_domain: url,
						is_wordpress: true
					}
				} );

			$.ajax( {
				url: 'https://sso.sharethis.com/register',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					result = results;

					// Create property.
					self.createProperty( result, url, '' );
				},
				error: function( xhr, status, error ) {
					$( '.st-loading-gif' ).hide();
					var message = xhr.responseJSON.message;

					$( 'div.error-message' ).html( '' );
					$( '.sharethis-account-creation small' ).after(
						'<div class="error-message" style="text-align: center; margin: 1rem 0;">' +
						message +
						'</div>'
					);
				}
			} );
		},

		/**
		 * Create property for new account.
		 *
		 * @param accountInfo
		 * @param url
		 */
		createProperty: function( accountInfo, url, type ) {
			var result = null,
				self = this,
				token = accountInfo.token,
				button = this.data.firstButton,
				theData;

			if ( 'string' === typeof accountInfo ) {
				token = accountInfo;
			}

			theData = JSON.stringify( {
				token: token,
				product: button + '-share-buttons',
				domain: url,
				is_wordpress: true
			} );

			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					result = results;

					self.setCredentials( result.secret, result._id, token, type );
				}
			} );
		},

		/**
		 * Load preview buttons.
		 *
		 * @param type
		 */
		loadPreview: function( type ) {
			var button = $( '.selected-button' ).attr( 'id' ),
				bAlignment = $( '.button-alignment .alignment-button[data-selected="true"]' )
					.attr( 'data-alignment' ),
				sAlignment = $( '.sticky-alignment' ).find( 'input' ).is( ':checked' ),
				bSize = $( '.button-size .item input:checked' ).siblings( 'label' ).html(),
				bLabels = $( '.button-labels .item input:checked' )
					.siblings( 'label' )
					.attr( 'id' ),
				bCount = $( 'input.minimum-count' ).val(),
				showTotal = $( 'span.show-total-count' )
					.siblings( 'div.switch' )
					.find( 'input' )
					.is( ':checked' ),
				extraSpacing = $( 'span.extra-spacing' )
					.siblings( 'div.switch' )
					.find( 'input' )
					.is( ':checked' ),
				showMobile = $( 'span.show-on-mobile' )
					.find( 'input' )
					.is( ':checked' ),
				showDesktop = $( 'span.show-on-desktop' )
					.find( 'input' )
					.is( ':checked' ),
				vertAlign = $( '.vertical-alignment' ).val() + 'px',
				mobileBreak = $( '.mobile-breakpoint' ).val(),
				spacing = 0,
				bRadius = $( '#radius-selector' ).val() + 'px',
				networks = [],
				language = $( '#st-language option:selected' ).val(),
				self = this,
				size,
				padding,
				fontSize,
				config;

			if ( undefined === button ) {
				return;
			}

			if ( 'initial' === type ) {
				$( '.share-buttons .share-button[data-selected="true"]' ).each( function( index ) {
					networks[ index ] = $( this ).attr( 'data-network' );
				} );
			} else {
				$( '.sharethis-selected-networks > div > div .st-btn' ).each( function( index ) {
					networks[ index ] = $( this ).attr( 'data-network' );
				} );
			}

			// If true alignment is right else its left.
			if ( sAlignment ) {
				sAlignment = 'right';
			} else {
				sAlignment = 'left';
			}

			if ( 'Small' === bSize ) {
				size = 32;
				fontSize = 11;
				padding = 8;

				$( '#radius-selector' ).attr( 'max', 16 );
			}

			if ( 'Medium' === bSize ) {
				size = 40;
				fontSize = 12;
				padding = 10;

				$( '#radius-selector' ).attr( 'max', 20 );
			}

			if ( 'Large' === bSize ) {
				size = 48;
				fontSize = 16;
				padding = 12;

				$( '#radius-selector' ).attr( 'max', 26 );
			}

			if ( extraSpacing ) {
				spacing = 8;
			}

			if ( 'Inline' === button ) {
				config = { alignment: bAlignment,
					enabled: true,
					font_size: fontSize,
					labels: bLabels,
					min_count: bCount,
					padding: padding,
					radius: bRadius,
					networks: networks,
					show_total: showTotal,
					show_mobile_buttons: true,
					size: size,
					spacing: spacing,
					language: language
				};
			} else {
				config = { alignment: sAlignment,
					enabled: true,
					labels: bLabels,
					min_count: bCount,
					radius: bRadius,
					networks: networks,
					mobile_breakpoint: mobileBreak,
					top: vertAlign,
					show_mobile: showMobile,
					show_total: showTotal,
					show_desktop: showDesktop,
					show_mobile_buttons: true,
					spacing: 0,
					language: language
				};
			}

			if ( 'submit' === type ) {
				wp.ajax.post( 'set_button_config', {
					button: button,
					config: config,
					nonce: this.data.nonce
				} ).always( function( results ) {
					window.location.href = '?page=sharethis-general&s=3';
				} );
			} else {
				$( '#' + button + '-8' ).html( '' );

				config.container = button + '-8';
				window.__sharethis__.href = 'https://www.sharethis.com/';
				window.__sharethis__.load( button.toLowerCase() + '-share-buttons', config );

				$( '.sharethis-selected-networks > div > div' ).sortable( {
					stop: function( event, ui ) {
						self.loadPreview( '' );
					}
				} );
			}
		},

		/**
		 * Get user information and property
		 *
		 * @param token
		 */
		getProperty: function( token ) {
			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/me?token=' + token,
				method: 'Get',
				async: false,
				contentType: 'application/json; charset=utf-8',
				success: function( result ) {
					$( '#sharethis-login-wrap' ).hide();
					$( '#sharethis-property-select-wrap' ).show();
					$( '#sharethis-properties' ).html( '' );

					$.each( result.properties, function( index, value ) {
						var config = { inline: value['inline-share-buttons'], sticky: value['sticky-share-buttons'] },
							firstProduct = value['onboarding_product'],
							inline = value['inline-share-buttons'],
							sticky = value['sticky-share-buttons'];

						if ( undefined !== sticky && 'sop' === firstProduct && sticky.enabled ) {
							firstProduct = 'sticky';
						}

						if ( undefined !== inline && 'sop' === firstProduct && inline.enabled ) {
							firstProduct = 'inline';
						}

						if ( undefined === inline && undefined === sticky ) {
							firstProduct = 'inline';
							config = { 'inline': { alignment: 'center',
								enabled: true,
								font_size: 11,
								labels: 'cta',
								min_count: 10,
								padding: 8,
								radius: 4,
								networks: ['facebook', 'twitter', 'pinterest', 'email', 'sms', 'sharethis'],
								show_total: true,
								size: 32,
								spacing: 8,
								language: 'en',
								} };
						}

						$( '#sharethis-properties' ).append( '<option data-first="' + firstProduct + '" data-config="' + JSON.stringify( config ).replace( /"/g, "'" ) + '" data-prop="' + value._id + '" value="' + value.secret + '">' + value.domain + '</option>' );
					} );
				}
			} );
		},

		/**
		 * Set button configurations
		 */
		setButtonConfig: function( secret, propertyid, token, type ) {
			var button = this.data.firstButton,
				config = this.data.buttonConfig,
        self = this,
        gdprEnabled = $( '#gdpr-checkbox' ).attr( 'checked' );

			if ( 'create' === type ) {
				config = this.$createConfig;
				button = 'inline';
			}

			// Make sure info is in proper case type.
			if ( 'inline' === button ) {
				config[ button ].size = parseInt( config[ button ].size );
				config[ button ].padding = parseInt( config[ button ].padding );
				config[ button ]['font_size'] = parseInt( config[ button ]['font_size'] );
			}

			// Make sure radius is sent in proper format.
			config[ button ].radius = parseInt( config[ button ].radius.toString().replace( 'px', '' ) );

			// Remove the preview override for mobile buttons.
			delete config[ button ]['show_mobile_buttons'];

			// Send new button status value.
			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property/product',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: JSON.stringify( {
					'secret': secret,
					'id': propertyid,
					'product': button + '-share-buttons',
					'config': config[ button ]
				} )
			} ).always( function( results ) {

				if ( 'create' === type ) {
					button = 'sticky';

					$.ajax( {
						url: 'https://platform-api.sharethis.com/v1.0/property/product',
						method: 'POST',
						async: false,
						contentType: 'application/json; charset=utf-8',
						data: JSON.stringify( {
							'secret': secret,
							'id': propertyid,
							'product': button + '-share-buttons',
							'config': config[ button ]
						} )
					} ).always( function( results ) {
					} );
				}

        if ( gdprEnabled ) {
          self.enableGDPR( token, propertyid );
        } else {
          window.location = '?page=sharethis-share-buttons';
        }
			} );
		},

    /**
     * Enable GDPR Compliance Tool
     */
    enableGDPR: function ( token, property ) {
      var config = {
        color: '#09cd18',
        display: 'always',
        enabled: true,
        language: 'en',
        publisher_name: '',
        publisher_purposes: [],
        scope: 'global'
      },
      self = this;

      // Send new button status value.
      $.ajax( {
        url: 'https://platform-api.sharethis.com/v1.0/property/product',
        method: 'POST',
        async: false,
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify( {
          'token': token,
          'id': property,
          'product': 'gdpr-compliance-tool-v2',
          'config': config
        } )
      } ).always( function ( configResults ) {
        wp.ajax.post( 'set_gdpr_config', {
          config: config,
          first: true,
          nonce: self.data.nonce
        } ).always( function( results ) {
          window.location = '?page=sharethis-share-buttons';
        } );
      } );
    },

		/**
		 * Check if ad blocker exists and notify if so.
		 */
		checkAdBlock: function() {
			$(document).ready(function(){
				if($("#detectadblock").height() > 0) {
				} else {
					$('#adblocker-notice').show();
				}
			});
		}
	};
} )( window.jQuery, window.wp );
