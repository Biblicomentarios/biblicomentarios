import StockpackImage from './stockpack-image';
import StockpackDialog from './stockpack-dialog';

const StockpackImages = wp.media.View.extend( {
	tagName: 'ul',
	className: 'attachments',

	attributes: {
		tabIndex: -1,
	},
	dialog: null,

	initialize() {
		this.el.id = _.uniqueId( '__stockpack-images-view-' );

		_.defaults( this.options, {
			sortable: false,
			refreshSensitivity: wp.media.isTouchDevice ? 300 : 200,
			refreshThreshold: 1.5,
			resize: true,
			idealColumnWidth:jQuery( window ).width() < 640 ? 135 : 150,
			provider: '',
		} );

		this._viewsByCid = {};
		this.$window = jQuery( window );
		this.resizeEvent = 'resize.media-modal-columns';
		this.fixScroll = _.debounce( this.maybeFixScroll.bind( this ), 700 );

		this.collection.on( 'add', function( attachment ) {
			this.views.add( this.createAttachmentView( attachment ), {
				at: this.collection.indexOf( attachment ),
			} );
			this.fixScroll();
		}, this );

		this.collection.on( 'remove', function( attachment ) {
			const view = this._viewsByCid[ attachment.cid ];
			delete this._viewsByCid[ attachment.cid ];

			if ( view ) {
				view.remove();
			}
		}, this );

		this.collection.on( 'reset', this.render, this );
		this.collection.on( 'query_error', _.debounce( this.queryError, 150 ), this );
		// Throttle the scroll handler and bind this.
		this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

		this.options.scrollElement = this.options.scrollElement || this.el;
		jQuery( this.options.scrollElement ).on( 'scroll', this.scroll );

		_.bindAll( this, 'setColumns' );

		if ( this.options.resize ) {
			this.on( 'ready', this.bindEvents );
			this.controller.on( 'open', this.setColumns );

			// Call this.setColumns() after this view has been rendered in the DOM so
			// attachments get proper width applied.
			_.defer( this.setColumns, this );
		}

		this.prepareDebounced = _.debounce( this.prepare.bind( this ), 700 );
	},

	/**
	 * @param {StockpackImage} attachment
	 * @return {wp.media.View} description
	 */
	createAttachmentView( attachment ) {
		const view = new StockpackImage( {
			model: attachment,
			collection: this.collection,
			selection: this.options.selection,
			controller: this.controller,
			provider: this.options.provider,
		} );

		return this._viewsByCid[ attachment.cid ] = view;
	},

	bindEvents() {
		this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.setColumns, 50 ) );
	},

	prepare() {
		// Create all of the Attachment views, and replace
		// the list in a single DOM operation.
		if ( this.collection.length ) {
			const view = this.collection.map( this.createAttachmentView, this );
			this.views.set( view );
			// If there are no elements, clear the views and load some.
		} else {
			this.views.unset();
			this.collection.more();
		}
	},

	maybeFixScroll() {
		if ( this.el.scrollHeight === this.el.clientHeight ) {
			this.scroll();
		}
	},

	scroll() {
		let el = this.options.scrollElement,
			scrollTop = el.scrollTop;

		// The scroll event occurs on the document, but the element
		// that should be checked is the document body.
		if ( el === document ) {
			el = document.body;
			scrollTop = jQuery( document ).scrollTop();
		}

		if ( ! jQuery( el ).is( ':visible' ) || ! this.collection.hasMore() ) {
			return;
		}

		const toolbar = this.views.parent.toolbar;

		// Show the spinner only if we are close to the bottom.
		if ( el.scrollHeight - ( scrollTop + el.clientHeight ) < el.clientHeight / 3 ) {
			toolbar.get( 'spinner' ).show();
		}

		if ( el.scrollHeight < scrollTop + ( el.clientHeight * this.options.refreshThreshold ) ) {
			this.collection.more().done( function() {
				// view.scroll();
				toolbar.get( 'spinner' ).hide();
			} );
		}
	},

	setColumns() {
		const prev = this.columns,
			width = this.$el.width();

		if ( width ) {
			this.columns = Math.min( Math.round( width / this.options.idealColumnWidth ), 12 ) || 1;

			if ( ! prev || prev !== this.columns ) {
				this.$el.closest( '.media-frame-content' ).attr( 'data-columns', this.columns );
			}
		}
	},

	show() {
		this.$el.removeClass( 'hidden' );
	},
	hide() {
		this.$el.addClass( 'hidden' );
	},

	queryError( error ) {
		const exception = error[ 0 ];

		switch ( exception.code ) {
			case 'premium_limit_reached' :
				this.premiumLimit( exception );
				break;
			case 'anonymous_limit_reached' :
				this.anonymousLimit( exception );
				break;
			case 'free_limit_reached' :
				this.freeLimit( exception );
				break;

			default:
				// do nothing
				break;
		}
	},

	freeLimit( exception ) {
		if ( jQuery( '#free-limit' ).length > 0 ) {
			return;
		}
		this.dialog = new StockpackDialog( {
			...wp.media.view.l10n.stockpack.limit.free,
			...{
				id: 'free-limit',
				error: exception.message,
				controller: this.controller,
			},
		},
		);

		this.dialog.open();
	},

	anonymousLimit( exception ) {
		if ( jQuery( '#anonymous-limit' ).length > 0 ) {
			return;
		}

		this.dialog = new StockpackDialog( {
			...wp.media.view.l10n.stockpack.limit.anonymous,
			...{
				id: 'anonymous-limit',
				error: exception.message,
				controller: this.controller,
			},
		},
		);

		this.dialog.open();
	},

	premiumLimit( exception ) {
		if ( jQuery( '#premium-limit' ).length > 0 ) {
			return;
		}
		this.dialog = new StockpackDialog( {
			...wp.media.view.l10n.stockpack.limit.premium,
			...{
				id: 'premium-limit',
				error: exception.message,
				controller: this.controller,
			},
		},
		);
		this.dialog.buttons( [
			{
				text: wp.media.view.l10n.stockpack.limit.cancel,
				click: () => {
					this.dialog.close();
				},
			},
			{
				text: wp.media.view.l10n.stockpack.limit.contact,
				click: () => {
					window.open( wp.media.view.settings.stockpack.contact_url, '_blank' );
				},
				class: 'button-primary',
			},
		] );
		this.dialog.open();
	},

} );

export default StockpackImages;
