jQuery( document ).ready( function( $ ) {

	var $element = jQuery( 'input.validate-stockpack-key' );

	function append_icon() {
		$element.after( '<span class="stockpack-verification"></span>' )
		$element.before( '<span class="stockpack-message">' + wp.media.view.l10n.stockpack.license + '</span>' )
	}

	function validate_key() {
		$element.parent().removeClass( 'valid' ).removeClass( 'invalid' );
		var val = $element.val();
		var options = options || {};
		options.data = _.extend( options.data || {}, {
			action: 'validate-stockpack',
			security: wp.media.view.settings.stockpack.nonce_validate,
			key: val,
		} );
		wp.media.ajax( options ).done( function( response ) {
			if ( response.status === 'passed' ) {
				$element.parent().removeClass( 'invalid' ).addClass( 'valid' )
			} else {
				$element.parent().removeClass( 'valid' ).addClass( 'invalid' )
			}
		} )
	}

	$element.keyup( _.debounce( validate_key, 250 ) );

	// run initially
	append_icon();
	validate_key();
} )
