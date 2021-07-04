import StockpackColorFilters from './stockpack-color-picker-filter';

const StockpackColorFilter = StockpackColorFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'advanced-filters color-filter',
	id: 'stockpack-color-filter',
	createFilters() {
		const filters = {};

		filters.color = {
			text: wp.media.view.l10n.stockpack.filters.color.text,
			props: {
				color: null,
			},
			priority: 20,
			id: 'stockpack-color-picker',
		};

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.color.text;
		this.name = 'color';
	},
} );
export default StockpackColorFilter;
