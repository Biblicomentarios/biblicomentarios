export default class StockpackUpgrade {
	/**
	 * @param {Object} $element
	 * @param {Object} controller
	 */
	constructor( $element, controller ) {
		this.$ = {};
		this.$.element = $element;
		this.$.iframe = this.$.element.find( 'iframe' );
		this.iframe = this.$.iframe[ 0 ].contentWindow;
		this.$.iframeStatus = this.$.element.find( '.iframe-status' );
		const State = Backbone.Model.extend( {
			defaults: {
				loggedIn: false,
				upgraded: false,
				page: '',
			},
		} );
		this.controller = controller;
		this.state = new State();
		this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.initial );
		this.bindState();
		this.bindMessages();
		this.bindUi();
	}

	bindState() {
		this.state.on( 'change:page', this.pageChanged, this );
	}

	bindUi() {
		this.$.iframe.on( 'load', () => {
			this.isLoggedIn();
		} );
		// if the iframe was already loaded, the above will not fire initially
		// so we call it
		this.pollUntilChange();
	}

	pollUntilChange() {
		if ( this.state.get( 'page' ) ) {
			return false;
		}
		this.isLoggedIn();
		setTimeout( () => {
			this.pollUntilChange();
		}, 3000 );
	}

	pollUntilChangeUpgraded() {
		if ( this.state.get( 'upgraded' ) ) {
			return false;
		}
		if ( this.state.get( 'page' ) !== '/billing' ) {
			return false;
		}
		this.getUpgraded();
		setTimeout( () => {
			this.pollUntilChangeUpgraded();
		}, 3000 );
	}

	pageChanged() {
		const page = this.state.get( 'page' );
		switch ( page ) {
			case '/billing':
				this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.billing );
				this.pollUntilChangeUpgraded();
				break;
			case '/login':
				this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.login );
				break;
			case '/register':
				this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.register );
				break;
			case '/providers':
				this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.providers );
				this.getToken();
				break;
			default:
				this.setStatus( wp.media.view.l10n.stockpack.limit.free.status.elsewhere );
		}
	}

	getToken() {
		this.sendMessage( {
			query: 'getToken',
		} );
	}

	receiveToken( token ) {
		const options = options || {};
		options.context = this;
		options.data = _.extend( options.data || {}, {
			action: 'token-stockpack',
			security:wp.media.view.settings.stockpack.nonce_token,
			token,
		} );
		wp.media.ajax( options ).done( () => {
			this.controller.trigger( 'account-upgraded' );
		} );
	}

	setStatus( status ) {
		this.$.iframeStatus.text( status );
	}

	sendMessage( message ) {
		this.iframe.postMessage( JSON.stringify( message ), '*' );
	}

	getUpgraded() {
		this.sendMessage( {
			query: 'hasUpgraded',
		} );
	}

	hasUpgraded( response ) {
		this.state.set( 'upgraded', response );
		if ( response ) {
			this.sendMessage( {
				query: 'goToProviders',
			} );
			this.pollUntilChange();
		}
	}

	isLoggedIn() {
		this.sendMessage( {
			query: 'isLoggedIn',
		} );
	}

	bindMessages() {
		window.addEventListener( 'message', ( e ) => {
			this.receiveMessage( e );
		} );
	}

	receiveMessage( event ) {
		let message = {};
		try {
			message = JSON.parse( event.data );
		} catch ( e ) {
			// system messages
			return false;
		}
		switch ( message.query ) {
			case 'isLoggedIn':
				this.state.set( 'loggedIn', message.value );
				this.state.set( 'page', message.page );
				break;
			case 'hasUpgraded':
				this.hasUpgraded( message.value );
				break;
			case 'getToken':
				this.receiveToken( message.value );
				break;
		}
	}

	destroy() {

	}
}
