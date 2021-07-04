const StockpackToolbar = wp.media.View.extend( /** @lends wp.media.view.Toolbar.prototype */{
	tagName: 'div',
	className: 'media-toolbar',

	initialize() {
		const state = this.controller.state(),
			selection = this.selection = state.get( 'stockpack-selection' ),
			library = this.library = state.get( 'stockpack-images' );

		this._views = {};

		// The toolbar is composed of two `PriorityList` views.
		this.primary = new wp.media.view.PriorityList();
		this.secondary = new wp.media.view.PriorityList();
		this.primary.$el.addClass( 'media-toolbar-primary search-form' );
		this.secondary.$el.addClass( 'media-toolbar-secondary' );

		this.views.set( [ this.secondary, this.primary ] );

		if ( this.options.items ) {
			this.set( this.options.items, { silent: true } );
		}

		if ( ! this.options.silent ) {
			this.render();
		}

		if ( selection ) {
			selection.on( 'add remove reset', this.refresh, this );
		}

		if ( library ) {
			library.on( 'add remove reset', this.refresh, this );
		}
	},
	/**
	 * @return {wp.media.view.Toolbar} Returns itsef to allow chaining
	 */
	dispose() {
		if ( this.selection ) {
			this.selection.off( null, null, this );
		}

		if ( this.library ) {
			this.library.off( null, null, this );
		}
		/**
		 * call 'dispose' directly on the parent class
		 */
		return wp.media.View.prototype.dispose.apply( this, arguments );
	},

	ready() {
		this.refresh();
	},

	/**
	 * @param {string} id
	 * @param {Backbone.View|Object} view
	 * @param {Object} [options={}]
	 * @return {wp.media.view.Toolbar} Returns itself to allow chaining
	 */
	set( id, view, options ) {
		let list;
		options = options || {};

		// Accept an object with an `id` : `view` mapping.
		if ( _.isObject( id ) ) {
			_.each( id, function( objectView, index ) {
				this.set( index, objectView, { silent: true } );
			}, this );
		} else {
			if ( ! ( view instanceof Backbone.View ) ) {
				view.classes = [ 'media-button-' + id ].concat( view.classes || [] );
				view = new wp.media.view.Button( view ).render();
			}

			view.controller = view.controller || this.controller;

			this._views[ id ] = view;

			list = view.options.priority < 0 ? 'secondary' : 'primary';
			this[ list ].set( id, view, options );
		}

		if ( ! options.silent ) {
			this.refresh();
		}

		return this;
	},

	/**
	 * @param {string} id
	 * @return {wp.media.view.Button} view
	 */
	get( id ) {
		return this._views[ id ];
	},
	/**
	 * @param {string} id
	 * @param {Object} options
	 * @return {wp.media.view.Toolbar} Returns itself to allow chaining
	 */
	unset( id, options ) {
		delete this._views[ id ];
		this.primary.unset( id, options );
		this.secondary.unset( id, options );

		if ( ! options || ! options.silent ) {
			this.refresh();
		}
		return this;
	},

	refresh() {
		const state = this.controller.state(),
			library = state.get( 'stockpack-images' ),
			selection = state.get( 'selection' );

		_.each( this._views, function( button ) {
			if ( ! button.model || ! button.options || ! button.options.requires ) {
				return;
			}

			const requires = button.options.requires;
			let disabled = false;

			// Prevent insertion of attachments if any of them are still uploading
			if ( selection && selection.models ) {
				disabled = _.some( selection.models, function( attachment ) {
					return attachment.get( 'uploading' ) === true;
				} );
			}

			if ( requires.selection && selection && ! selection.length ) {
				disabled = true;
			} else if ( requires.library && library && ! library.length ) {
				disabled = true;
			}
			button.model.set( 'disabled', disabled );
		} );
	},
} );

export default StockpackToolbar;
