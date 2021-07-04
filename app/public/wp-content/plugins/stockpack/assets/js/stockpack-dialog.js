import StockpackTimer from './utils/stockpack-timer';
import StockpackRegister from './utils/stockpack-register';
import StockpackUpgrade from './utils/stockpack-upgrade';

const StockpackDialog = wp.media.View.extend( /** @lends wp.media.view.Toolbar.prototype */{
	tagName: 'div',
	className: 'stockpack-dialog fusion-builder-dialog',
	template: wp.template( 'stockpack-dialog' ),

	events: {
		'click .ui-widget-overlay': 'close',
		'click .ui-dialog-titlebar-close': 'close',
	},

	defaults: {
		id: 'stockpack-dialog',
		title: '',
		message: '',
		link: '',
		error: '',
		iframe: '',
		state: '',
		external: '',
		externalUrl: '',
		directLicenseUrl: '',
	},

	timer: null,
	register: null,
	upgrade: null,
	controller: null,

	initialize() {
		if ( this.options.controller ) {
			this.controller = this.options.controller;
			delete this.options.controller;
		}

		this.model = new Backbone.Model( this.defaults );
		// If any of the `options` have a key from `defaults`, apply its
		// value to the `model` and remove it from the `options object.
		_.each( this.defaults, function( def, key ) {
			const value = this.options[ key ];
			if ( _.isUndefined( value ) ) {
				return;
			}

			this.model.set( key, value );
			delete this.options[ key ];
		}, this );

		this.insertElement();

		this.listenTo( this.model, 'change', this.render );
		this.createDialog();
		this.maybeCreateTimer();
		this.maybeHookRegister();
		this.maybeHookUpgrade();
	},

	maybeCreateTimer() {
		if ( this.$el.find( '.stockpack-timer' ).length ) {
			this.timer = new StockpackTimer( this.$el.find( '.stockpack-timer' ) );
		}
	},

	maybeHookRegister() {
		const $iframe = this.$el.find( 'iframe#stockpack-token-iframe' );
		if ( $iframe.length > 0 ) {
			this.register = new StockpackRegister( $iframe.parent(), this.controller );
			this.controller.on( 'token-updated', _.bind( this.startRetry, this ) );
		}
	},

	maybeHookUpgrade() {
		const $iframe = this.$el.find( 'iframe#stockpack-upgrade-iframe' );
		if ( $iframe.length > 0 ) {
			this.upgrade = new StockpackUpgrade( $iframe.parent(), this.controller );
			this.controller.on( 'account-upgraded', _.bind( this.startRetry, this ) );
		}
	},

	startRetry() {
		this.controller.trigger( 'start-retry' );
		this.close();
	},

	createDialog() {
		this.$el.data( 'initialized', 'true' ).dialog( {
			title: this.model.get( 'title' ),
			dialogClass: this.dialogClass(),
			autoOpen: false,
			draggable: false,
			width: 'auto',
			modal: true,
			resizable: false,
			closeOnEscape: true,
			close: () => {
				this.close();
			},
			position: {
				my: 'center',
				at: 'center',
				of: window,
			},
		} );
	},

	setStatus( status ) {
		this.model.set( 'state', status );
		this.render();
	},

	setExternalUrl( externalUrl ) {
		this.model.set( 'externalUrl', externalUrl );
		this.render();
	},

	buttons( options ) {
		this.$el.dialog( 'option', 'buttons', options );
	},

	insertElement() {
		const $dialog = jQuery( '<div id="' + this.model.get( 'id' ) + '" data-title="' + this.model.get( 'title' ) + '"></div> ' );
		jQuery( 'body' ).append( $dialog );
		this.setElement( $dialog );
		this.render();
	},

	dispose() {
		if ( this.$el.data( 'initialized' ) ) {
			this.$el.dialog( 'destroy' );
		}
		this.$el.remove();
		if ( this.timer ) {
			this.timer.destroy();
		}
		if ( this.register ) {
			this.register.destroy();
		}
		if ( this.upgrade ) {
			this.upgrade.destroy();
		}
	},

	prepare() {
		return this.model.toJSON();
	},

	close() {
		if ( this.$el.data( 'initialized' ) ) {
			this.$el.dialog( 'close' );
		}
		this.dispose();
	},
	open() {
		this.$el.dialog( 'open' );
	},

	dialogClass() {

		let classes = 'wp-dialog stockpack-dialog ';

		function getQuery( q ) {
			return ( window.location.search.match( new RegExp( '[?&]' + q + '=([^&]+)' ) ) || [ , null ] )[ 1 ];
		}

		if ( getQuery( 'fb-edit' ) ) {
			classes += 'fusion-builder-dialog';
		}

		return classes;

	}


} );

export default StockpackDialog;
