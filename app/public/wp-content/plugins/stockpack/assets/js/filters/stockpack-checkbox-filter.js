const StockpackCheckboxFilters = wp.media.View.extend( /** @lends wp.media.view.AttachmentFilters.prototype */{
	className: 'checkbox-stockpack-filters',

	keys: [],
	checked: [],

	initialize() {
		this.createFilters();
		this.createChecked();
		_.extend( this.filters, this.options.filters );
		const $mainLabel = jQuery( '<span></span>', { text: this.label, class: 'group-label' } );
		// Build `<option>` elements.
		this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
			const $el = jQuery( '<input />', { type: 'checkbox', value, id: filter.id } );
			const t = this;
			$el.on( 'change', function( e ) {
				t.checkboxChange( jQuery( this ), e );
			} );
			const $label = jQuery( '<label />', { for: filter.id, text: filter.text } );
			const $wrapper = jQuery( '<span>', { class: 'checkbox-component' } ).append( $el ).append( $label );
			return {
				el: $wrapper[ 0 ],
				priority: filter.priority || 50,
			};
		}, this ).sortBy( 'priority' ).pluck( 'el' ).value() );
		this.$el.prepend( $mainLabel );
		this.$el.wrap( '<div class="' + this.className + '"></div>' );
		this.controller.on( 'update-filters', _.bind( this.updateFilters, this ) );
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
		this.model.set( this.name, this.getValue() );
	},

	getValue() {
		if ( ! this.checked.length ) {
			return null;
		}
		if ( ! this.multiple && this.checked.length > 1 ) {
			return this.default;
		}
		if ( this.multiple ) {
			return this.checked;
		}

		return this.checked[ 0 ];
	},

	checkboxChange( $element ) {
		const value = $element.val();
		const checked = $element.is( ':checked' );
		if ( checked ) {
			this.checked.push( value );
		} else {
			this.checked = this.checked.filter( function( val ) {
				return value !== val;
			} );
		}
	},
} );

export default StockpackCheckboxFilters;
