const StockpackAttribution = wp.media.View.extend( /** @lends wp.media.view.Toolbar.prototype */{
	tagName: 'div',
	className: 'stockpack-attribution',
	template: wp.template( 'stockpack-attribution' ),
	events: {
		'click .notice-dismiss': 'dismiss',
	},
	defaults: {
		id: 'stockpack-attribution',
		message: '',
		author_info: '',
		link: '',
		link_title: '',
	},
	dismissed: false,

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

		this.updateProvider( 'default' );

		this.controller.on( 'update_provider', _.debounce( this.updateProvider, 150 ), this );
	},

	show() {
		if ( ! this.dismissed ) {
			this.$el.removeClass('hidden')
		}
	},

	hide() {
		this.$el.addClass('hidden')
	},

	dispose() {
		this.$el.remove();
	},

	dismiss() {
		this.$el.hide();
		this.dismissed = true;
	},

	prepare() {
		return this.model.toJSON();
	},

	updateProvider( provider ) {
		const data = wp.media.view.l10n.stockpack.attribution[ provider ];
		_.each( data, function( value, key ) {
			this.model.set( key, value );
		}, this );
		this.render();
	},

} );

export default StockpackAttribution;
