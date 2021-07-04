import StockpackSelectFilters from './stockpack-select-filter';

const slugify = require( 'slugify' );
const StockpackProviderFilters = StockpackSelectFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'provider-filter',
	id: 'stockpack-provider-filter',
	hidden: false,
	createFilters() {
		const filters = {};

		let priority = 1;
		filters.default = {
			text: wp.media.view.l10n.stockpack.filters.provider.default,
			props: {
				provider: null,
			},
			priority,
			id: 'stockpack-provider-default',
		};
		const values = wp.media.view.l10n.stockpack.filters.provider.values;
		for ( const [ key, value ] of Object.entries( values ) ) {
			if ( values.hasOwnProperty( key ) ) {
				priority++;
				filters[ key ] = {
					text: value,
					props: {
						provider: key,
					},
					priority,
					id: 'stockpack-provider-' + slugify( key, '_' ),
				};
			}
		}

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.provider.label;
		this.name = 'provider';
		this.default = null;
	},
	events: {
		change: 'triggerFilter',
	},

	render() {
		if ( wp.media.view.l10n.stockpack.filters.provider.values.length === 0 ) {
			this.$el.hide();
			this.hidden = true;
		}
		let val = this.model.escape( 'provider' );
		if ( val ) {
			this.$el.find( 'select' ).val( val );
		}
		return this;
	},

	triggerFilter() {
		this.controller.trigger( 'update-filters', this.model, event.currentTarget );
	},
} );
export default StockpackProviderFilters;
