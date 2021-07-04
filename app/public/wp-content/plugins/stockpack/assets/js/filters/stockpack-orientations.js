import StockpackCheckboxFilters from './stockpack-checkbox-filter';

const StockpackOrientationFilters = StockpackCheckboxFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'advanced-filters orientation-filter',
	id: 'stockpack-gender-filter',
	createFilters() {
		const filters = {};

		filters.horizontal = {
			text: wp.media.view.l10n.stockpack.filters.orientation.horizontal,
			props: {
				orientation: 'horizontal',
			},
			priority: 20,
			id: 'stockpack-orientation-horizontal',
		};

		filters.vertical = {
			text: wp.media.view.l10n.stockpack.filters.orientation.vertical,
			props: {
				orientation: 'vertical',
			},
			priority: 30,
			id: 'stockpack-orientation-vertical',
		};

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.orientation.label;
		this.name = 'orientation';
		this.default = null;
		this.multiple = false;
	},
} );
export default StockpackOrientationFilters;
