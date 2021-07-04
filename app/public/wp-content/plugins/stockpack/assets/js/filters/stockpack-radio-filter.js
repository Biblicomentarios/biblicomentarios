const StockpackRadioFilters = wp.media.View.extend( /** @lends wp.media.view.AttachmentFilters.prototype */{
	className: 'radio-stockpack-filters',

	keys: [],
	checked: [],

	initialize() {
		this.createFilters();
		this.createChecked();
		_.extend( this.filters, this.options.filters );
		const $mainLabel = jQuery( '<span></span>', { text: this.label, class: 'group-label' } );
		// Build `<option>` elements.
		this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
			const $el = jQuery( '<input />', {
				type: 'radio',
				name: filter.name,
				value,
				id: filter.id,
				checked: filter.checked,
			} );
			const t = this;
			$el.on( 'change', function( e ) {
				t.radioChange( jQuery( this ), e );
			} );
			const $label = jQuery( '<label />', { for: filter.id, text: filter.text } );
			const $wrapper = jQuery( '<span>', { class: 'radio-component' } ).append( $el ).append( $label );
			return {
				el: $wrapper[ 0 ],
				priority: filter.priority || 50,
			};
		}, this ).sortBy( 'priority' ).pluck( 'el' ).value() );
		this.$el.prepend( $mainLabel );
		this.$el.wrap( '<div class="' + this.className + '"></div>' );
		this.controller.on( 'update-filters', _.bind( this.updateFilters, this ) );
		this.radioChange( this.$el );
	},

	/**
	 * @abstract
	 */
	createFilters() {
		this.filters = {};
	},

	/**
	 * @abstract
	 */
	createChecked() {
		this.checked = [];
	},

	updateFilters() {
		this.model.set( this.getValue() );
	},

	getValue() {
		if ( ! this.checked.length ) {
			return null;
		}

		const filter = this.filters[ this.checked ];
		if ( filter ) {
			return filter.props;
		}
	},

	radioChange( $element ) {
		this.checked = $element.val();
	},
} );

export default StockpackRadioFilters;
