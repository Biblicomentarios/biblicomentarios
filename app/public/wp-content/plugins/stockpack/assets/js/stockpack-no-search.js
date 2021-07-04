const StockpackNoSearch = wp.media.View.extend( /** @lends wp.media.view.Toolbar.prototype */{
	tagName: 'div',
	className: 'stockpack-no-search',
	template: wp.template( 'stockpack-no-search' ),

	defaults: {
		message: '',
		inspiration: [],
	},

	initialize() {
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
		this.setInspiration();
		this.listenTo( this.model, 'change', this.render );
	},

	setInspiration() {
		const random = this.getRandomInt( 1, 8 );

		this.model.set( 'unsure', this.model.get( 'inspiration' ).unsure );
		const inspiration = this.model.get( 'inspiration' ).random[ 'item_' + random ];
		this.model.set( 'illustration', inspiration.image );
		this.model.set( 'response', inspiration.message );
	},

	render() {
		wp.Backbone.View.prototype.render.apply( this, arguments );
		return this;
	},

	getRandomInt( min, max ) {
		min = Math.ceil( min );
		max = Math.floor( max );
		return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
	},

	dispose() {
		this.$el.remove();
		if ( this.timer ) {
			this.timer.destroy();
		}
	},

	prepare() {
		return this.model.toJSON();
	},

	show() {
		this.$el.removeClass( 'hidden' );
	},
	hide() {
		this.$el.addClass( 'hidden' );
	},

} );

export default StockpackNoSearch;
