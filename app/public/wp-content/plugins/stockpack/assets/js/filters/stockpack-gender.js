import StockpackCheckboxFilters from './stockpack-checkbox-filter';

const StockpackGenderFilters = StockpackCheckboxFilters.extend( /** @lends wp.media.view.AttachmentFilters.All.prototype */{
	className: 'advanced-filters gender-filter',
	id: 'stockpack-gender-filter',
	createFilters() {
		const filters = {};

		filters.female = {
			text: wp.media.view.l10n.stockpack.filters.gender.female,
			props: {
				people_gender: 'female',
			},
			priority: 20,
			id: 'stockpack-gender-female',
		};

		filters.male = {
			text: wp.media.view.l10n.stockpack.filters.gender.male,
			props: {
				people_gender: 'male',
			},
			priority: 30,
			id: 'stockpack-gender-male',
		};

		this.filters = filters;
		this.label = wp.media.view.l10n.stockpack.filters.gender.label;
		this.name = 'people_gender';
		this.default = 'both';
		this.multiple = false;
	},
} );
export default StockpackGenderFilters;
