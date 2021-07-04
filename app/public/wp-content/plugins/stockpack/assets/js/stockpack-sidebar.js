const StockpackSidebar = wp.media.View.extend( {
	tagName: 'div',
	className: 'media-sidebar stockpack-sidebar',

	initialize() {
		this._views = {};

		this.set( _.extend( {}, this._views, this.options.views ), { silent: true } );
		delete this.options.views;

		if ( ! this.options.silent ) {
			this.render();
		}
	},
	/**
	 * @param {string} id
	 * @param {wp.media.View|Object} view
	 * @param {Object} options
	 * @return {wp.media.view.PriorityList} Returns itself to allow chaining
	 */
	set( id, view, options ) {
		let index;
		options = options || {};

		// Accept an object with an `id` : `view` mapping.
		if ( _.isObject( id ) ) {
			_.each( id, function( objectView, key ) {
				this.set( key, objectView );
			}, this );
			return this;
		}

		if ( ! ( view instanceof Backbone.View ) ) {
			view = this.toView( view, id, options );
		}
		view.controller = view.controller || this.controller;

		this.unset( id );

		const priority = view.options.priority || 10;
		const views = this.views.get() || [];

		_.find( views, function( existing, i ) {
			if ( existing.options.priority > priority ) {
				index = i;
				return true;
			}
		} );

		this._views[ id ] = view;
		this.views.add( view, {
			at: _.isNumber( index ) ? index : views.length || 0,
		} );

		return this;
	},
	/**
	 * @param {string} id
	 * @return {wp.media.View} view
	 */
	get( id ) {
		return this._views[ id ];
	},
	/**
	 * @param {string} id
	 * @return {wp.media.view.PriorityList} view
	 */
	unset( id ) {
		const view = this.get( id );

		if ( view ) {
			view.remove();
		}

		delete this._views[ id ];
		return this;
	},
	/**
	 * @param {Object} options
	 * @return {wp.media.View} view
	 */
	toView( options ) {
		return new wp.media.View( options );
	},
} );

export default StockpackSidebar;
