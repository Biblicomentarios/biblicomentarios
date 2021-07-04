/**
 * ShareThis Share Buttons
 *
 * @package ShareThisShareButtons
 */

/* exported ShareButtons */
var ShareButtons = ( function( $, wp ) {
  'use strict';

  return {
    /**
     * Holds data.
     */
    data: {},

    /**
     * Boot plugin.
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
      this.$container = $( '.sharethis-wrap' );
      this.$resultWrapper = $( '#category-result-wrapper' );

      // Set temp enables.
      this.$tempEnable = { 'inline': false, 'sticky': false, 'gdpr': false };

      // Get and set current accounts platform configurations to global.
      this.$config = this.getConfig();

      this.listen();
      this.createReset();

      // Check if platform has changed its button config.
      this.checkIfChanged();

      // Check if buttons are enabled or disabled on both ends.
      this.markSelected();

      // Check if adblock is enabled. Shows notice if so.
      this.checkAdBlock();
    },

    /**
     * Initiate listeners.
     */
    listen: function() {
      var self = this,
        timer = '';

      // Scroll to anchor in vendor list.
      // Send user input to category search AFTER they stop typing.
      $('body').on( 'keyup', '.vendor-search input', function( e ) {
        clearTimeout( timer );

        timer = setTimeout( function() {
          self.scrollToAnchor($(this).val());
        }.bind( this ), 500 );
      } );

      // Toggle button menus when arrows are clicked.
      $( 'body' ).on( 'click', '.accor-wrap .accor-tab', function() {
        var type = $( this ).find( 'span.accor-arrow' );

        self.updateAccors( type.html(), type );
      } );

      // New color select.
      this.$container.on('click', "#sharethis-form-color .color", function() {
        $('#sharethis-form-color .color').removeClass('selected');
        $(this).addClass('selected');
      });

      // clear or show choices.
      this.$container.on('click', '#clear-choices', function(e) {
        e.preventDefault();
        e.stopPropagation();

        $( '.purpose-item input' ).prop( 'checked', false );
      });

      // clear or show choices.
      this.$container.on('click', '#see-st-choices', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.purpose-item input[name="purposes[1]"]').prop('checked', true);
        $('.purpose-item input[name="purposes[3]"][value="consent"]').prop('checked', true);
        $('.purpose-item input[name="purposes[5]"][value="consent"]').prop('checked', true);
        $('.purpose-item input[name="purposes[6]"][value="consent"]').prop('checked', true);
        $('.purpose-item input[name="purposes[9]"][value="legitimate"]').prop('checked', true);
        $('.purpose-item input[name="purposes[10]"][value="legitimate"]').prop('checked', true);
      });

      // Uncheck radio if click on selected box.
      this.$container.on( 'click', '.gdpr-platform .lever', ( e ) => {
        e.preventDefault();
        e.stopPropagation();

        const theInput = $( e.currentTarget ).siblings( 'input' );

        if ( 'select-purpose' === theInput.attr( 'name' ) && !theInput.is( ':checked' ) ) {
          if ( 'consent' === theInput.val() ) {
            $( '.purpose-item input' ).prop( 'checked', false );
            $( '.empty-choices' ).addClass( 'engage' );
          } else {
            $( '.purpose-item input[name="purposes[1]"]' ).prop( 'checked', true );
            $( '.purpose-item input[name="purposes[3]"][value="consent"]' ).prop( 'checked', true );
            $( '.purpose-item input[name="purposes[5]"][value="consent"]' ).prop( 'checked', true );
            $( '.purpose-item input[name="purposes[6]"][value="consent"]' ).prop( 'checked', true );
            $( '.purpose-item input[name="purposes[9]"][value="legitimate"]' ).prop( 'checked', true );
            $( '.purpose-item input[name="purposes[10]"][value="legitimate"]' ).prop( 'checked', true );
          }

          theInput.prop( 'checked', true )

          $( '.back-next' ).addClass( 'engage' );

          return;
        }

        if ( theInput.is( ':checked' ) ) {
          $( `input[name="${theInput.attr( 'name' )}"]` ).prop( 'checked', false )
        } else {
          theInput.prop( 'checked', true )
        }
      } );

      // On off button events.
      this.$container.on( 'click', '.share-on, .share-off', function() {

        // Revert to default color.
        $( this ).closest( 'div' ).find( 'div.label-text' ).css( 'color', '#8d8d8d' );

        // Change the input selected color to white.
        $( this ).find( '.label-text' ).css( 'color', '#ffffff' );
      } );

      // Copy text from read only input fields.
      this.$container.on( 'click', '#copy-shortcode, #copy-template', function() {
        self.copyText( $( this ).closest( 'div' ).find( 'input' ) );
      } );

      // Open close options and update platform and WP on off status.
      this.$container.on( 'click', '.enable-buttons .share-on, .enable-buttons .share-off', function() {
        var button = $( this ).closest( 'div' ).attr( 'id' ),
          type = $( this ).find( 'div.label-text' ).html();

        self.updateButtons( button, type, 'click' );
      } );

      // Toggle button menus when arrows are clicked.
      this.$container.on( 'click', 'span.st-arrow', function() {
        var button = $( this ).attr( 'id' ),
          type = $( this ).html();

        self.updateButtons( button, type, '' );
      } );

      // Click reset buttons.
      this.$container.on( 'click', 'p.submit #reset', function() {
        var type = $( this )
          .closest( 'p.submit' )
          .prev()
          .find( '.enable-buttons' )
          .attr( 'id' );

        self.setDefaults( type );
      } );

      // Send user input to category search AFTER they stop typing.
      this.$container.on( 'keyup', 'input#category-ta, input#page-ta', function( e ) {
        var type = $( this ).siblings( '.search-st-icon' ).attr( 'id' ),
          result = '#' + type + '-result-wrapper';

        clearTimeout( timer );

        timer = setTimeout( function() {
          self.returnResults( $( this ).val(), type, result );
        }.bind( this ), 500 );
      } );

      // Force search when search icon is clicked.
      this.$container.on( 'click', '.search-st-icon', function() {
        var type = $( this ).attr( 'id' ),
          key = $( this ).siblings( 'input' ).val(),
          result = '#' + type + '-result-wrapper';

        self.returnResults( key, type, result );
      } );

      // Select an item to exclude. Add it to list.
      this.$container.on( 'click', '.ta-page-item, .ta-category-item', function() {
        var type = $( this )
          .closest( '.list-wrapper' )
          .find( 'span.search-st-icon' )
          .attr( 'id' );

        self.updateOmit( $( this ), type );
      } );

      // Remove excluded item from list.
      this.$container.on( 'click', '.remove-omit', function() {
        $( this ).closest( 'li.omit-item' ).remove();
      } );

      // Toggle margin control buttons.
      this.$container.on( 'click', 'button.margin-control-button', function() {
        var status = $( this ).hasClass( 'active-margin' );

        self.activateMargin( this, status );
      } );

      $( 'body' ).on( 'click', '.item #cta, .item #counts, .item #none, .item #medium, .item #small, .item #large', function() {
        var checked = $( this ).siblings( 'input' ).is( ':checked' ),
          button = $( this ).closest( '.selected-button' ).attr( 'id' );

        $( '.sharethis-inline-share-buttons' ).removeClass( 'st-has-labels' );

        if ( ! checked ) {
          $( this ).closest( '.st-radio-config' ).find( '.item' ).each( function() {
            $( this ).find( 'input' ).prop( 'checked', false );
          } );

          $( this ).siblings( 'input' ).prop( 'checked', true );
        }

        self.loadPreview( '', button );
      } );

      // All levers.
      this.$container.on( 'click', '.item div.switch', function() {
        var button = $( this ).closest( '.selected-button' ).attr( 'id' );

        self.loadPreview( '', button );
      } );

      // Minimum count.
      this.$container.on( 'change', 'input.minimum-count, #radius-selector, .vertical-alignment, .mobile-breakpoint', function() {
        var button = $( this ).closest( '.selected-button' ).attr( 'id' );

        self.loadPreview( '', button );
      } );

      // Languages.
      this.$container.on( 'change', '#st-language-inline, #st-language-sticky', function() {
        var id = $( this ).attr( 'id' ),
          language = $( this ).find( 'option:selected' ).val();

        if ( 'st-language-inline' === id ) {
          $( '#st-language-sticky option[value="' + language + '"]' ).prop( 'selected', true );
        } else {
          $( '#st-language-inline option[value="' + language + '"]' ).prop( 'selected', true );
        }

        self.loadPreview( '', 'sticky' );
        self.loadPreview( '', 'inline' );
      } );

      // Button alignment.
      this.$container.on( 'click', '.button-alignment .alignment-button', function() {
        var button = $( this ).closest( '.selected-button' ).attr( 'id' );

        $( '.sharethis-inline-share-buttons' ).removeClass( 'st-justified' );
        $( '.button-alignment .alignment-button[data-selected="true"]' )
          .attr( 'data-selected', 'false' );
        $( this ).attr( 'data-selected', 'true' );

        self.loadPreview( '', button );
      } );

      // Select or deselect a network.
      this.$container.on( 'click', '.share-buttons .share-button', function() {
        var selection = $( this ).attr( 'data-selected' ),
          button = $( this ).closest( '.selected-button' ).attr( 'id' ),
          network = $( this ).attr( 'data-network' );

        if ( 'true' === selection ) {
          $( this ).attr( 'data-selected', 'false' );
          $( '.' + button + '-platform .sharethis-selected-networks > div > div div[data-network="' + network + '"]' ).remove();
        } else {
          $( this ).attr( 'data-selected', 'true' );
          $( '.' + button + '-platform .sharethis-selected-networks > div > div' ).append( '<div class="st-btn" data-network="' + network + '" style="display: inline-block;"></div>' );
        }

        self.loadPreview( '', button );
      } );

      // Submit configurations.
      $( '.sharethis-wrap form' ).submit( function(e) {
        self.loadPreview( 'submit', 'inline' );
        self.loadPreview( 'submit', 'sticky' );
        self.loadPreview( 'submit', 'gdpr' );
      } );

      // Add class to preview when scrolled to.
      $( window ).on( 'scroll load', function() {
        var vTop = $( '.vertical-alignment' ).val() + 'px',
          stickyTop,
          stopPoint,
          gdprTop = parseInt( $( 'form .form-table:last-of-type' ).offset().top ) - 100,
          inlineTop = $( '.inline-platform' ).offset().top;

        // Start fixed inline preview when top of browser reached.
        if ( $( window ).scrollTop() >= inlineTop && $( window ).scrollTop() <= gdprTop ) {
          $( '.sharethis-inline-share-buttons' ).addClass( 'sharethis-prev-stick' );
        } else {
          $( '.sharethis-inline-share-buttons' ).removeClass( 'sharethis-prev-stick' );
        }

        // Stop the sticky preview when top of sticky config reached.
        stickyTop = parseInt( $( 'form .form-table:nth-of-type(2)' ).offset().top ) - 100;
        stopPoint = stickyTop + parseInt( vTop );

        if ( $( window ).scrollTop() >= stickyTop && $( window ).scrollTop() <= gdprTop ) {
          $( '.sharethis-sticky-share-buttons .st-sticky-share-buttons' )
            .css( { 'display':'block', 'position': 'fixed', 'top': vTop } );
          $( '.sharethis-sticky-share-buttons .st-sticky-share-buttons.st-left' )
            .removeClass( 'stuck-buttons' );
          $( '.sharethis-inline-share-buttons' ).removeClass( 'sharethis-prev-stick' );
        } else {
          $( '.sharethis-sticky-share-buttons .st-sticky-share-buttons' )
            .css( { 'display': 'none' } );
          $( '.sharethis-sticky-share-buttons .st-sticky-share-buttons.st-left' )
            .addClass( 'stuck-buttons' );
        }
      } );
    },

    /**
     * Change font color of selected buttons.
     * Also decide whether to update WP enable / disable status or just show / hide menu options.
     */
    markSelected: function() {
      var sConfigSet = null !== this.$config && undefined !== this.$config['sticky-share-buttons'],
        iConfigSet = null !== this.$config && undefined !== this.$config['inline-share-buttons'],
        gConfigSet = null !== this.$config && undefined !== this.$config['gdpr-compliance-tool-v2'],
        iturnOn,
        iturnOff,
        sturnOn,
        sturnOff,
        stickyEnable,
        inlineEnable,
        gturnOn,
        gturnOff,
        gdprEnable;

      // Check if api call is successful and if sticky buttons are enabled.  Use WP data base if not.
      if ( sConfigSet ) {
        stickyEnable = this.$config['sticky-share-buttons']['enabled']; // Dot notation cannot be used due to dashes in name.
      } else {
        if ( undefined !== this.data.buttonConfig['sticky'] ) {
          stickyEnable = this.data.buttonConfig['sticky']['enabled'];
        }
      }

      // Check if api call is successful and if inline buttons are enabled.  Use WP data base if not.
      if ( iConfigSet ) {
        inlineEnable = this.$config['inline-share-buttons']['enabled']; // Dot notation cannot be used due to dashes in name.
      } else {
        if ( undefined !== this.data.buttonConfig['inline'] ) {
          inlineEnable = this.data.buttonConfig['inline']['enabled'];
        }
      }

      // Check if api call is successful and if gdpr is enabled. Use WP data base if not.
      if ( gConfigSet ) {
        gdprEnable = this.$config['gdpr-compliance-tool-v2']['enabled']; // Dot notation cannot be used due to dashes in name.
      } else {
        if ( undefined !== this.data.buttonConfig['gdpr'] ) {
          gdprEnable = this.data.buttonConfig['gdpr']['enabled'];
        }
      }

      // Decide whether to update WP database or just show / hide menu options.
      if ( ! iConfigSet || (
        undefined !== this.data.buttonConfig['inline'] && this.data.buttonConfig['inline']['enabled'] === this.$config['inline-share-buttons']['enabled'] ) ) { // Dot notation cannot be used due to dashes in name.
        iturnOn = 'show';
        iturnOff = 'hide';
      } else {
        iturnOn = 'On';
        iturnOff = 'Off';
      }

      // If enabled show button configuration.
      if ( 'true' === inlineEnable || true === inlineEnable ) {
        $( '.inline-platform' ).css( 'display', 'table-footer-group' );
        this.updateButtons( 'Inline', iturnOn );
        $( '#Inline label.share-on input' ).prop( 'checked', true );
      } else {
        $( '.inline-platform' ).hide();
        this.updateButtons( 'Inline', iturnOff );
        $( '#Inline label.share-off input' ).prop( 'checked', true );
      }

      // Decide whether to update WP database or just show / hide menu options.
      if ( ! sConfigSet || ( undefined !== this.data.buttonConfig['sticky'] && this.data.buttonConfig['sticky']['enabled'] === this.$config['sticky-share-buttons']['enabled'] ) ) { // Dot notation cannot be used due to dashes in name.
        sturnOn = 'show';
        sturnOff = 'hide';
      } else {
        sturnOn = 'On';
        sturnOff = 'Off';
      }

      // If enabled show sticky options.
      if ( 'true' === stickyEnable || true === stickyEnable ) {
        $( '.sticky-platform' ).css( 'display', 'table-footer-group' );
        this.updateButtons( 'Sticky', sturnOn );
        $( '#Sticky label.share-on input' ).prop( 'checked', true );
      } else {
        $( '.sticky-platform' ).hide();
        this.updateButtons( 'Sticky', sturnOff );
        $( '#Sticky label.share-off input' ).prop( 'checked', true );
      }

      // Decide whether to update WP database or just show / hide menu options.
      if ( ! gConfigSet || ( undefined !== this.data.buttonConfig['gdpr'] && this.data.buttonConfig['gdpr']['enabled'] === this.$config['gdpr-compliance-tool-v2']['enabled'] ) ) { // Dot notation cannot be used due to dashes in name.
        gturnOn = 'show';
        gturnOff = 'hide';
      } else {
        gturnOn = 'On';
        gturnOff = 'Off';
      }

      // If enabled show gdpr options.
      if ( 'true' === gdprEnable || true === gdprEnable ) {
        $( '.gdpr-platform' ).css( 'display', 'table-footer-group' );
        this.updateButtons( 'Gdpr', gturnOn );
        $( '#Gdpr label.share-on input' ).prop( 'checked', true );
      } else {
        $( '.gdpr-platform' ).hide();
        this.updateButtons( 'Gdpr', gturnOff );
        $( '#Gdpr label.share-off input' ).prop( 'checked', true );
      }

      // Change button font color based on status.
      $( '.share-on input:checked, .share-off input:checked' ).closest( 'label' ).find( 'span.label-text' ).css( 'color', '#ffffff' );
    },

    /**
     * Check the platform has updated the button configs.
     */
    checkIfChanged: function() {
      var iTs = this.$config['inline-share-buttons'],
        sTs = this.$config['sticky-share-buttons'],
        gTs = this.$config['gdpr-compliance-tool-v2'],
        myITs = this.data.buttonConfig['inline'],
        mySTs = this.data.buttonConfig['sticky'],
        myGTs = this.data.buttonConfig['gdpr'],
        theConfig;

      // Set variables if array exists.
      if ( undefined !== iTs ) {
        iTs = iTs['updated_at'];

        if ( undefined !== iTs ) {
          iTs = iTs.toString();
        }
      }

      if ( undefined !== sTs ) {
        sTs = sTs['updated_at'];

        if ( undefined !== sTs ) {
          sTs = sTs.toString();
        }
      }

      if ( undefined !== gTs ) {
        gTs = gTs['updated_at'];

        if ( undefined !== gTs ) {
          gTs = gTs.toString();
        }
      }

      if ( undefined !== myITs ) {
        myITs = myITs['updated_at'];
      }

      if ( undefined !== mySTs ) {
        mySTs = mySTs['updated_at'];
      }

      if ( undefined !== myGTs ) {
        myGTs = myGTs['updated_at'];
      }

      // If platform has updated the button config or platform configs are broken use WP config.
      if ( iTs !== myITs || undefined === this.data.buttonConfig ) {
        this.setConfigFields( 'inline', this.$config['inline-share-buttons'], 'platform' );
      } else {
        this.loadPreview( 'initial', 'inline' );
      }

      if ( sTs !== mySTs || undefined === this.data.buttonConfig ) {
        this.setConfigFields( 'sticky', this.$config['sticky-share-buttons'], 'platform' );
      } else {
        this.loadPreview( 'initial', 'sticky' );
      }

      if ( gTs !== myGTs || undefined === this.data.buttonConfig ) {
        this.setConfigFields( 'gdpr', this.$config['gdpr-compliance-tool-v2'], 'platform' );
      } else {
        this.loadPreview( 'initial', 'gdpr' );
      }
    },

    /**
     * Show button configuration.
     *
     * @param button
     * @param type
     * @param event
     */
    updateButtons: function( button, type, event ) {
      var when = 'last-of-type',
        pTypes = [ 'show', 'On', '►', 'true' ],
        aTypes = [ 'show', 'hide', '►', '▼' ],
        timer = '';

      // Determine which style.
      if ( 'Inline' === button ) {
        when = 'first-of-type';
      }

      // If Sticky.
      if ( 'Sticky' === button ) {
        when = 'nth-of-type(2)';
      }

      // If not one of the show types then hide.
      if ( -1 !== $.inArray( type, pTypes ) ) {

        // Show the button configs.
        $( '.sharethis-wrap form .form-table:' + when + ' tr' ).not( ':eq(0)' ).show();

        // Show the submit / reset buttons.
        $( '.sharethis-wrap form .submit:' + when ).show();

        // Change the icon next to title.
        $( '.sharethis-wrap h2:' + when + ' span' ).html( '&#9660;' );

        // Platform config.
        $( '.' + button.toLowerCase() + '-platform' ).css( 'display', 'table-footer-group' );

        if ( 'click' === event ) {
          this.loadPreview( 'turnon', button );
        }

        // Set option value for button.
        wp.ajax.post( 'update_buttons', {
          type: button.toLowerCase(),
          onoff: 'On',
          nonce: this.data.nonce
        } ).always( function() {
        } );
      } else {

        // Hide the button configs.
        $( '.sharethis-wrap form .form-table:' + when + ' tr' ).not( ':eq(0)' ).hide();

        // Hide the submit / reset buttons.
        $( '.sharethis-wrap form .submit:' + when ).hide();

        // Change the icon next to title.
        $( '.sharethis-wrap h2:' + when + ' span' ).html( '&#9658;' );

        // Platform config.
        $( '.' + button.toLowerCase() + '-platform' ).hide();

        if ( 'click' === event ) {
          this.loadPreview( 'turnoff', button );
        }

        // Set option value for button.
        wp.ajax.post( 'update_buttons', {
          type: button.toLowerCase(),
          onoff: 'Off',
          nonce: this.data.nonce
        } ).always( function() {
        } );
      }
    },

    /**
     * Update buttons on platform
     *
     * @param button
     * @param type
     */
    updatePlatform: function( button, type ) {
      var status,
        buttonConfig,
        self = this,
        button = button.toLowerCase();

      // Set status variable to bool.
      if ( 'On' === type ) {
        status = true;
      } else {
        status = false;
      }

      // Default button config with enable.
      if ( 'inline' === button ) {
        buttonConfig = { "enabled": ( true === status ), "alignment" : "center", "font_size" : 12, "has_spacing" : true, "labels" : "cta", "min_count" : 10, "networks" : [ "facebook", "twitter", "pinterest", "email", "sms", "sharethis" ], "num_networks" : 6, "padding" : 10, "radius" : 4, "show_total" : true, "size" : 40, "size_label" : "medium", "spacing" : 8 }
      } else {
        buttonConfig = { "enabled": ( true === status ), "alignment" : "left", "labels" : "cta", "min_count" : 10, "mobile_breakpoint" : 1024, "networks" : [ "facebook", "twitter", "pinterest", "email", "sms", "sharethis" ], "num_networks" : 6, "padding" : 12, "radius" : 4, "show_mobile" : true, "show_toggle" : true, "show_total" : true, "size" : 48, "top" : 160 }
      }
    },

    /**
     * Copy text to clipboard
     *
     * @param copiedText
     */
    copyText: function( copiedText ) {
      copiedText.select();
      document.execCommand( 'copy' );
    },

    /**
     * Add the reset buttons to share buttons menu
     */
    createReset: function() {
      var button = '<input type="button" id="reset" class="button button-primary" value="Reset">',
        newButtons = $( '.sharethis-wrap form .submit' ).append( button ).clone(),
        newButtons2 = $( '.sharethis-wrap form .submit' ).clone();

      // Add new cloned reset button to inline menu list.
      $( '.sharethis-wrap form .form-table:first-of-type' ).after( newButtons );
      $( newButtons ).find( '#submit:first-of-type' ).addClass( 'st-submit-2' );
      $( '.sharethis-wrap form .form-table:nth-of-type(2)' ).after( newButtons2 );
      $( newButtons2 ).find( '#submit:nth-of-type(2)' ).addClass( 'st-submit-3' );
    },

    /**
     * Set to default settings when reset is clicked.
     *
     * @param type
     */
    setDefaults: function( type ) {
      wp.ajax.post( 'set_default_settings', {
        type: type,
        nonce: this.data.nonce
      } ).always( function() {
        if ( 'both' !== type ) {
          location.href = location.pathname + '?page=sharethis-share-buttons&reset=' + type;
        } else {
          location.reload();
        }
      } );
    },

    /**
     * Send input value and return LIKE categories/pages.
     *
     * @param key
     * @param type
     * @param result
     */
    returnResults: function( key, type, result ) {
      wp.ajax.post( 'return_omit', {
        key: key,
        type: type,
        nonce: this.data.nonce
      } ).always( function( results ) {
        if ( '' !== results ) {
          $( result ).show().html( results );
        } else {
          $( result ).hide();
        }
      }.bind( this ) );
    },

    /**
     * Add / remove selected omit item to omit list.
     *
     * @param value
     * @param type
     */
    updateOmit: function( value, type ) {
      var result = '#' + type + '-current-omit',
        wrapper = '#' + type + '-result-wrapper';

      // Hide the results when item is selected and add it to list.
      $( wrapper ).hide();
      $( result ).append( '<li class="omit-item">' + value.html() + '<span id="' + value.attr( "data-id" ) + '" class="remove-omit">X</span><input type="hidden" name="sharethis_sticky_' + type + '_off[' + value.html() + ']" value="' + value.attr( "data-id" ) + '" id="sharethis_sticky_' + type + '_off[' + value.html() + ']" value="' + value.attr( "data-id" ) + '" /></li>' );
    },

    /**
     * Get current config data from user.
     */
    getConfig: function() {
      var result = null,
        callExtra = 'secret=' + this.data.secret;

      if ( 'undefined' === this.data.secret || undefined === this.data.secret ) {
        callExtra = 'token=' + this.data.token;
      }

      $.ajax( {
        url: 'https://platform-api.sharethis.com/v1.0/property/?' + callExtra + '&id=' + this.data.propertyid,
        method: 'GET',
        async: false,
        contentType: 'application/json; charset=utf-8',
        success: function( results ) {
          result = results;
        }
      } );

      return result;
    },

    /**
     * Activate specified option margin controls and show/hide
     *
     * @param marginButton
     * @param status
     */
    activateMargin: function( marginButton, status ) {
      if ( ! status ) {
        $( marginButton ).addClass( 'active-margin' ).find( 'span.margin-on-off' ).html( 'On' );
        $( marginButton ).siblings( 'div.margin-input-fields' ).show().find( 'input' ).prop( 'disabled', false );
      } else {
        $( marginButton ).removeClass( 'active-margin' ).find( 'span.margin-on-off' ).html( 'Off' );
        $( marginButton ).siblings( 'div.margin-input-fields' ).hide().find( 'input' ).prop( 'disabled', true );
      }
    },

    /**
     * Load preview buttons.
     *
     * @param type
     * @param button
     */
    loadPreview: function( type, button ) {
      if ( 'initial' === type ) {
        this.setConfigFields( button, '', '' );
      }

      var bAlignment = $( '.button-alignment .alignment-button[data-selected="true"]' ).attr( 'data-alignment' ),
        sAlignment = $( '.sticky-alignment' ).find( 'input' ).is( ':checked' ),
        self = this,
        bSize = $( '.button-size .item input:checked' ).siblings( 'label' ).html(),
        bLabels = $( '#' + button + ' .button-labels .item input:checked' ).siblings( 'label' ).attr( 'id' ),
        bCount = $( '#' + button + ' input.minimum-count' ).val(),
        showTotal = $( '#' + button + ' span.show-total-count' ).siblings( 'div.switch' ).find( 'input' ).is( ':checked' ),
        extraSpacing = $( 'div.extra-spacing' ).find( 'input' ).is( ':checked' ),
        showMobile = $( 'div.show-on-mobile' ).find( 'input' ).is( ':checked' ),
        showDesktop = $( 'div.show-on-desktop' ).find( 'input' ).is( ':checked' ),
        vertAlign = $( '.vertical-alignment' ).val() + 'px',
        mobileBreak = $( '.mobile-breakpoint' ).val(),
        spacing = 0,
        bRadius = $( '#' + button + ' #radius-selector' ).val() + 'px',
        language = $( '#st-language-' + button + ' option:selected' ).val(),
        publisherPurpose = $('#publisher-purpose input:checked'),
        publisherPurposes = [],
        display = $('#sharethis-user-type option:selected').val(),
        name = $('#sharethis-publisher-name').val(),
        scope = $( '#sharethis-consent-type option:selected' ).val(),
        color = $( '#sharethis-form-color .color.selected' ).attr('data-value'),
        languageGDPR = $( '#st-language' ).val(),
        networks,
        size,
        padding,
        fontSize,
        config,
        beforeConfig,
        theFirst = false,
        wpConfig,
        upConfig,
        theData,
        enabled = false,
        buttonCode = button.toLowerCase();

      // Set button var.
      button = 'inline' === buttonCode || 'sticky' === buttonCode ? buttonCode + '-share-buttons' : 'gdpr-compliance-tool-v2';

      if ( 'initial' === type && undefined !== this.data.buttonConfig[ buttonCode ] ) {
        networks = this.data.buttonConfig[ buttonCode ]['networks'];
      } else {
        networks = [];

        $( '#' + buttonCode + '-8 > div .st-btn' ).each( function ( index ) {
          networks[index] = $( this ).attr( 'data-network' );
        } );
      }

      if ( 'sync-platform' === type && undefined !== this.$config[ button ] ) {
        networks = this.$config[ button ]['networks'];
      }

      // If newly turned on use selected networks.
      if ( 'turnon' === type || undefined !== this.data.buttonConfig[ buttonCode ] && undefined === this.data.buttonConfig[ buttonCode ]['networks'] ) {
        networks = [];

        $( '.' + buttonCode + '-platform .share-buttons .share-button[data-selected="true"]' ).each( function ( index ) {
          networks[index] = $( this ).attr( 'data-network' );
        } );
      }

      if ( 'submit' === type ) {
        networks = [];

        $( '#' + buttonCode + '-8 > div .st-btn' ).each( function ( index ) {
          networks[index] = $( this ).attr( 'data-network' );
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

      // If submited or turned on make sure enabled setting is set properly.
      if ( undefined !== this.$config[ button ] && undefined !== this.$config[ button ]['enabled'] ) {
        enabled = 'true' === this.$config[ button ]['enabled'] ||
                  true === this.$config[ button ]['enabled'] ||
                  true === this.$tempEnable[ buttonCode ];
      } else {
        enabled = false;
      }

      if ( 'inline' === buttonCode ) {
        config = { alignment: bAlignment,
          enabled: enabled,
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
          language: language,
        };
      }
      if ( 'sticky' === buttonCode ) {
        config = { alignment: sAlignment,
          enabled: enabled,
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
          language: language,
        };
      }

      if ('gdpr-compliance-tool-v2' === button) {
        var publisherPurposes = [],
            publisherRestrictions = {};

        $('#publisher-purpose input:checked').each( function( index, value ) {
          var theId = $(value).attr('data-id'),
            legit = 'consent' !== $(value).val();

          publisherPurposes.push({ 'id': theId, 'legitimate_interest' : legit });
        });

        $('.vendor-table-cell-wrapper label input:checked').each( function( index, value ) {
          publisherRestrictions[$(value).attr('data-id')] = true;
        });

        config = {
          enabled: enabled,
          display: display,
          publisher_name: name,
          publisher_purposes: publisherPurposes,
          publisher_restrictions: publisherRestrictions,
          language: languageGDPR,
          color: color,
          scope: scope,
        };
      }

      // Set config for initial post.
      beforeConfig = config;

      var types = ['submit', 'initial-platform', 'turnon', 'turnoff'];

      if ( types.includes( type ) ) {

        // If submiting WP keep platform timestamp if exists.
        if ( 'submit' === type && undefined !== this.$config[ button ] && undefined !== this.$config[ button ]['updated_at'] ) {
          config['updated_at'] = this.$config[ button ]['updated_at'];
        }

        // If platform different from WP.
        if ( 'initial-platform' === type ) {
          config = this.$config[ button ];

          if ( undefined === this.data.buttonConfig || true === this.data.buttonConfig ) {
            theFirst = 'upgrade';
          }
        }

        // If first load ever.
        if ( 'initial-platform' === type && undefined !== this.data.buttonConfig[ buttonCode ] && undefined === this.data.buttonConfig[ buttonCode ]['updated_at'] && undefined !== this.$config[ button ]['updated_at'] ) {
          config = beforeConfig;
          config['updated_at'] = this.$config[ button ]['updated_at'];
          config['networks'] = this.data.buttonConfig[ buttonCode ]['networks'];
        }

        if ( 'turnon' === type ) {
          config['enabled'] = true;
          config['radius'] = 4;
          config['show_total'] = true;
          config['labels'] = 'cta';
          config['min_count'] = 10;
          config['networks'] = ['facebook', 'twitter', 'pinterest', 'email', 'sms', 'sharethis'];

          $.each( config['networks'], function( index, value ) {
            $( '.' + buttonCode + '-network-list .share-button[data-network="' + value + '"]' ).attr( 'data-selected', 'true' );
          } );

          // Set temp enable to true.
          this.$tempEnable[ buttonCode ] = true;
        }

        if ( 'turnoff' === type ) {
          config['enabled'] = false;

          // Set temp enable to false.
          this.$tempEnable[ buttonCode ] = false;
        }

        if ( 'upgrade' === theFirst ) {
          upConfig = {
            inline: this.$config['inline-share-buttons'],
            sticky: this.$config['sticky-share-buttons'],
            gdpr: this.$config['gdpr-compliance-tool-v2'],
          };

          wp.ajax.post( 'set_button_config', {
            button: 'platform',
            config: upConfig,
            first: theFirst,
            type: 'login',
            nonce: this.data.nonce
          } ).always( function ( results ) {
            location.reload();
          }.bind( this ) );
        } else {
          wp.ajax.post( 'set_button_config', {
            button: buttonCode,
            config: config,
            first: false,
            nonce: this.data.nonce
          } ).always( function ( results ) {
            config['show_mobile_buttons'] = false;

            if ( 'initial-platform' !== type || (
              undefined !== this.data.buttonConfig[buttonCode] && undefined === this.data.buttonConfig[buttonCode]['updated_at']
            ) ) {
              config['enabled'] = ( 'true' === config['enabled'] || true === config['enabled'] );

              delete config['container'];
              delete config['id'];
              delete config['has_spacing'];
              delete config['show_mobile_buttons'];

              config['radius'] = undefined !== config['radius'] ? parseInt( config['radius'].toString().replace( 'px', '' ) ) : '';
              config['min_count'] = parseInt( config['min_count'] );

              if ( 'sticky' === buttonCode ) {
                config['mobile_breakpoint'] = parseInt( config['mobile_breakpoint'] );
                config['top'] = parseInt( config['top'].toString().replace( 'px', '' ) );
              }

              theData = {
                'id': this.data.propertyid,
                'product': button,
                'config': config
              };


              if ( 'undefined' === this.data.secret || undefined === this.data.secret ) {
                theData['token'] = this.data.token;
              } else {
                theData['secret'] = this.data.secret;
              }

              theData = JSON.stringify( theData );

              // Send new button status value.
              $.ajax( {
                url: 'https://platform-api.sharethis.com/v1.0/property/product',
                method: 'POST',
                async: false,
                contentType: 'application/json; charset=utf-8',
                data: theData,
                success: function () {
                  if ( 'turnon' === type ) {
                    location.reload();
                  }
                }
              } );
            }
          }.bind( this ) );
        }
      }

      if ( '' !== type ) {
        if ( 'inline' === buttonCode ) {

          // Convert pieces to integers.
          config['size'] = parseInt( config['size'] );
          config['font_size'] = parseInt( config['font_size'] );
          config['padding'] = parseInt( config['padding'] );
          config['spacing'] = parseInt( config['spacing'] );
          config['has_spacing'] = (
            0 === config['spacing']
          );
        }
      }

      // Make sure mobile button override is set.
      config['show_mobile_buttons'] = true;

      $( '#' + buttonCode + '-8' ).html( '' );

      config.container = buttonCode + '-8';

      window.__sharethis__.href = 'https://www.sharethis.com/';

      window.__sharethis__.load( button, config );

      $( '#' + buttonCode + '-8 > div' ).sortable( {
        stop: function( event, ui ) {
          self.loadPreview( '', buttonCode );
        }
      } );
    },

    /**
     * Set the settings fields for the button configurations.
     *
     * @param button
     */
    setConfigFields: function( button, config, type ) {
      var size;

      if ( '' === config ) {
        config = this.data.buttonConfig[ button ];
      }

      if (button === 'gdpr' && undefined !== config) {
        this.setGDPRConfig(config, type)
      }

      if ( undefined === config || undefined === config['radius'] ) {
        return;
      }

      $( '.' + button + '-network-list .share-button' ).each( function() {
        $( this ).attr( 'data-selected', false );
      } );

      // Networks.
      $.each( config['networks'], function( index, value ) {
        $( '.' + button + '-network-list .share-button[data-network="' + value + '"]' ).attr( 'data-selected', 'true' );
      } );

      // Labels.
      $( '#' + button + ' .button-labels .item input' ).prop( 'checked', false );
      $( '#' + button + ' .button-labels #' + config['labels'] ).siblings( 'input' ).prop( 'checked', true );

      // Counts.
      $( '#' + button + ' input.minimum-count' ).val( config['min_count'] );
      $( '#' + button + ' span.show-total-count' ).siblings( 'div.switch' ).find( 'input' ).prop( 'checked', ( undefined !== config['show_total'] && 'true' === config['show_total'].toString() ) );

      // Corners.
      if ( parseInt( config['radius'].toString().replace( 'px', '' ) ) > $( '#' + button + ' #radius-selector' ).attr( 'max' ) ) {
        $( '#' + button + ' #radius-selector' ).attr( 'max', config['radius'].toString().replace( 'px', '' ) );
        $( '#' + button + ' #radius-selector' ).val( config['radius'].toString().replace( 'px', '' ) );
      } else {
        $( '#' + button + ' #radius-selector' ).val( config['radius'].toString().replace( 'px', '' ) );
      }

      // Language.
      $( '#st-language-inline option[value="' + config['language'] + '"]' ).prop( 'selected', true );
      $( '#st-language-sticky option[value="' + config['language'] + '"]' ).prop( 'selected', true );

      if ( 'inline' === button ) {

        // Alignment.
        $( '.button-alignment .alignment-button[data-selected="true"]' ).attr( 'data-selected', 'false' );
        $( '.button-alignment .alignment-button[data-alignment="' + config['alignment'] + '"]' ).attr( 'data-selected', 'true' );

        // Size.
        $( '.button-size .item input' ).prop( 'checked', false );

        if ( '32' === config['size'].toString() ) {
          size = '#small';
        }

        if ( '40' === config['size'].toString() ) {
          size = '#medium';
        }

        if ( '48' === config['size'].toString() ) {
          size = '#large';
        }

        $( '.button-size ' + size ).siblings( 'input' ).prop( 'checked', true );

        // Extra spacing.
        $( 'div.extra-spacing' ).find( 'input' ).prop( 'checked', ( 0 !== config['spacing'] && '0' !== config['spacing'] ) );
      }

      if ( 'sticky' === button ) {

        // Alignment.
        if ( 'right' === config['alignment'] ) {
          $( '.sticky-alignment' ).find( 'input' ).prop( 'checked', true );
        }

        // Vertical alignment.
        $( '.vertical-alignment' ).val( config['top'].toString().replace( 'px', '' ) );

        // Mobile breakpoint.
        $( '.mobile-breakpoint' ).val( config['mobile_breakpoint'] );

        // Show on mobile.
        $( 'div.show-on-mobile' ).find( 'input' ).prop( 'checked', ( undefined !== config['show_mobile'] && 'true' === config['show_mobile'].toString() ) );

        // Show on desktop.
        $( 'div.show-on-desktop' ).find( 'input' ).prop( 'checked', ( undefined !== config['show_desktop'] && 'true' === config['show_desktop'].toString() ) );
      }

      if ( 'platform' === type ) {
        this.loadPreview( 'initial-platform', button );
      }
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
    },

    /**
     * Returns gdpr onboarding config values.
     */
    setGDPRConfig: function(config, type) {
      $('#sharethis-publisher-name').val(config['publisher_name']);
      $(`#sharethis-user-type option[value="${config['display']}"]` ).attr('selected',true);
      $(`#sharethis-consent-type option[value="${config['scope']}"]`).attr('selected', true);
      $(`#sharethis-form-color .color[data-value="${config['color']}"]`).addClass('selected');
      $(`#st-language option[value="${config['language']}"]`).attr('selected', true);

      $( "#publisher-purpose .purpose-item input" ).prop('checked', false);
      $( ".vendor-table-cell-wrapper input" ).prop('checked', false);

      if (undefined !== config['publisher_purposes']) {
        config['publisher_purposes'].map( ( purpVal ) => {
          var legit = 'true' === purpVal['legitimate_interest'] || true === purpVal['legitimate_interest'];
          var consent = 'false' === purpVal['legitimate_interest'] || false === purpVal['legitimate_interest'];

          $( `#publisher-purpose .purpose-item input[name="purposes[${purpVal.id}]"][value="legitimate"]` ).prop( 'checked', legit );
          $( `#publisher-purpose .purpose-item input[name="purposes[${purpVal.id}]"][value="consent"]` ).prop( 'checked', consent );
        } );
      }

      if (undefined !== config['publisher_restrictions']) {
        $.map(config['publisher_restrictions'], function (id, venVal ) {
          if(id) {
            $( `input[type="checkbox"][data-id="${venVal}"]` ).prop( 'checked', true );
          }
        } );
      }

      if ( 'platform' === type ) {
        this.loadPreview( 'initial-platform', 'gdpr' );
      }
    },
    /**
     * Toggle the accordions.
     *
     * @param type
     * @param arrow
     */
    updateAccors: function( type, arrow ) {
      var closestButton = $( arrow ).parent( '.accor-tab' ).parent( '.accor-wrap' );

      if ( '►' === type ) {

        // Show the button configs.
        closestButton.find( '.accor-content' ).slideDown();

        // Change the icon next to title.
        closestButton.find( '.accor-arrow' ).html( '&#9660;' );
      } else {

        // Show the button configs.
        closestButton.find( '.accor-content' ).slideUp();

        // Change the icon next to title.
        closestButton.find( '.accor-arrow' ).html( '&#9658;' );
      }
    },
    scrollToAnchor: function(aid) {
      var aTag = $("a[name='"+ aid.toLowerCase() +"']");

      $('.vendor-table-body').animate({
        scrollTop: 0
      }, 0).animate({
        scrollTop: aTag.offset().top - 3000
      }, 0);
    },
  };
} )( window.jQuery, window.wp );
