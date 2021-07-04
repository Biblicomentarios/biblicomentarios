import StockpackRadioFilters from './stockpack-radio-filter';

const StockpackSafeFilters = StockpackRadioFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'advanced-filters safe-filter',
	id: 'stockpack-safe-filter',
	createFilters() {
		const filters = {};

		filters.yes = {
			text: wp.media.view.l10n.stockpack.filters.safe.yes,
			props: {
				safe: true,
			},
			name: 'safe-search-stockpack',
			priority: 20,
			id: 'stockpack-safe-yes',
		};

		filters.no = {
			text: wp.media.view.l10n.stockpack.filters.safe.no,
			props: {
				safe: false,
			},
			priority: 30,
			name: 'safe-search-stockpack',
			id: 'stockpack-safe-no',
		};

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.safe.label;
		this.name = 'safe';
	},
} );
export default StockpackSafeFilters;
