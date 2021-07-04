const StockpackSearch = wp.media.View.extend( /** @lends wp.media.view.Search.prototype */{
	tagName: 'input',
	className: 'search',
	id: 'stockpack-search-input',

	attributes: {
		type: 'search',
		placeholder: wp.media.view.l10n.stockpack.search,
	},

	events: {
		input: 'search',
		keyup: 'search',
	},

	/**
	 * @return {wp.media.view.Search} Returns itself to allow chaining
	 */
	render() {
		this.el.value = this.model.escape( 'search' );
		return this;
	},

	search: _.debounce( function( event ) {
		if ( event.target.value ) {
			this.model.set( 'search', event.target.value );
		} else {
			this.model.unset( 'search' );
		}
	}, 500 ),
} );

export default StockpackSearch;
