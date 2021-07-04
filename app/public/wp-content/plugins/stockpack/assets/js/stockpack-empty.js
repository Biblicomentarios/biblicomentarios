import StockpackTimer from './utils/stockpack-timer';

const StockpackEmpty = wp.media.View.extend( /** @lends wp.media.view.Toolbar.prototype */{
	tagName: 'div',
	className: 'stockpack-empty',
	template: wp.template( 'stockpack-empty' ),

	events: {
		'click .retry': 'retry',
	},

	defaults: {
		message: '',
		link: false,
		retry: true,
	},

	timer: null,

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
		this.listenTo( this.model, 'change', this.render );

		this.controller.on( 'start-retry', _.bind( this.retry, this ) );
	},

	render() {
		wp.Backbone.View.prototype.render.apply( this, arguments );
		this.afterRender();
		return this;
	},

	afterRender() {
		this.maybeCreateTimer();
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

	maybeCreateTimer() {
		if ( this.$el.find( '.stockpack-timer' ).length ) {
			this.timer = new StockpackTimer( this.$el.find( '.stockpack-timer' ) );
		}
	},

	retry() {
		this.options.browser.updateContent();
	},

	show() {
		this.$el.removeClass( 'hidden' );
	},
	hide() {
		this.$el.addClass( 'hidden' );
	},

} );

export default StockpackEmpty;
