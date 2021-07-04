const StockpackColorPickerFilter = wp.media.View.extend( /** @lends wp.media.view.AttachmentFilters.prototype */{
	className: 'color-picker-stockpack-filters',

	keys: [],
	checked: [],

	initialize() {
		this.createFilters();
		_.extend( this.filters, this.options.filters );
		const $mainLabel = jQuery( '<span></span>', { text: this.label, class: 'group-label' } );
		// Build `<option>` elements.
		this.$el.html( _.chain( this.filters ).map( function( filter ) {
			const $el = jQuery( '<input />', { type: 'text', id: filter.id, class: 'stockpack-color-picker' } );

			return {
				el: $el[ 0 ],
				priority: filter.priority || 50,
			};
		}, this ).sortBy( 'priority' ).pluck( 'el' ).value() );
		this.$el.find( 'input' ).wpColorPicker();
		this.$el.prepend( $mainLabel );

		this.$el.wrap( '<div class="' + this.className + '"></div>' );
		this.controller.on( 'update-filters', _.bind( this.change, this ) );
	},

	/**
	 * @abstract
	 */
	createFilters() {
		this.filters = {};
	},

	/**
	 * When the selected filter changes, update the Attachment Query properties to match.
	 */
	change() {
		const value = this.getValue();
		if ( ! value || value === '' ) {
			this.model.set( this.name, null );
			return this;
		}
		this.model.set( this.name, value );
		return this;
	},

	getValue() {
		return this.$el.find( 'input' ).val();
	},
} );

export default StockpackColorPickerFilter;
