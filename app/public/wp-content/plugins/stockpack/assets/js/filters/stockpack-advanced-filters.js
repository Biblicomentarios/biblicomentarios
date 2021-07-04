const StockpackAdvancedFilters = wp.media.View.extend( /** @lends wp.media.view.Search.prototype */{
	tagName: 'div',
	className: 'stockpack-advanced-filters',
	id: 'stockpack-advanced-filters',

	attributes: {},

	initialize() {
		this._views = {};

		// The toolbar is composed of two `PriorityList` views.
		this.filters = new wp.media.view.PriorityList();
		this.actions = new wp.media.view.PriorityList();
		this.actions.$el.addClass( 'stockpack-filters-actions' );
		this.filters.$el.addClass( 'stockpack-filters-sub-container' );

		this.views.set( [ this.filters, this.actions ] );

		if ( this.options.items ) {
			this.set( this.options.items, { silent: true } );
		}

		if ( ! this.options.silent ) {
			this.render();
		}
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
			_.each( id, function( subView, subId ) {
				this.set( subId, subView, { silent: true } );
			}, this );
		} else {
			if ( ! ( view instanceof Backbone.View ) ) {
				view.classes = [ 'media-button-' + id ].concat( view.classes || [] );
				view = new wp.media.view.Button( view ).render();
			}

			view.controller = view.controller || this.controller;

			this._views[ id ] = view;

			list = view.options.priority < 0 ? 'filters' : 'actions';
			this[ list ].set( id, view, options );
		}

		if ( ! options.silent ) {
			this.refresh();
		}

		return this;
	},
	/**
	 * @param {string} id
	 * @return {wp.media.view.Button} button
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
		this.link.unset( id, options );
		this.filters.unset( id, options );

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

export default StockpackAdvancedFilters;
