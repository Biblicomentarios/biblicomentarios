import StockpackSelectFilters from './stockpack-select-filter';
const slugify = require( 'slugify' );
const StockpackCategoryFilters = StockpackSelectFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'advanced-filters category-filter',
	id: 'stockpack-category-filter',
	createFilters() {
		const filters = {};

		let priority = 1;
		filters.default = {
			text: wp.media.view.l10n.stockpack.filters.categories.default,
			props: {
				category: null,
			},
			priority,
			id: 'stockpack-category-default',
		};
		const values = wp.media.view.l10n.stockpack.filters.categories.values;
		for ( const [ key, value ] of Object.entries( values ) ) {
			if ( values.hasOwnProperty( key ) ) {
				priority++;
				filters[ key ] = {
					text: value,
					props: {
						category: key,
					},
					priority,
					id: 'stockpack-category-' + slugify( key ),
				};
			}
		}

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.categories.label;
		this.name = 'category';
		this.default = null;
	},
} );
export default StockpackCategoryFilters;
