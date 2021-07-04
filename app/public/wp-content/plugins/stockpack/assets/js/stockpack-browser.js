import StockpackImages from './stockpack-images';
import StockpackToolbar from './stockpack-toolbar';
import StockpackSearch from './filters/stockpack-search';
import StockpackDetails from './stockpack-details';
import StockpackSidebar from './stockpack-sidebar';
import StockpackEmpty from './stockpack-empty';
// import StockpackDialog from './stockpack-dialog';
// import StockpackGenderFilter from './filters/stockpack-gender';
import StockpackDownloader from './stockpack-downloader';
// import StockpackAdvancedLink from './filters/stockpack-advanced-trigger';
// import StockpackAdvancedFilters from './filters/stockpack-advanced-filters';
// import StockpackAdvanced from './filters/stockpack-advanced';
// import StockpackFilterButton from './filters/stockpack-filter-button';
// import StockpackOrientationFilter from './filters/stockpack-orientations';
// import StockpackImageTypeFilter from './filters/stockpack-image-type';
// import StockpackCategoryFilters from './filters/stockpack-category';
// import StockpackSafeFilters from './filters/stockpack-safe';
// import StockpackColorFilter from './filters/stockpack-color';
// import StockpackFilterClose from './filters/stockpack-filter-close';
import StockpackAttribution from './stockpack-attribution';
import StockpackNoSearch from './stockpack-no-search';
import StockpackProviderFilters from "./filters/stockpack-provider";

const StockpackBrowser = wp.media.View.extend( /** @lends wp.media.view.AttachmentsBrowser.prototype */{
	tagName: 'div',
	className: 'stockpack-browser attachments-browser',
	attribution: null,

	initialize() {
		_.defaults( this.options, {
			search: true,
			provider: false,
		} );

		this.createToolbar();
		this.createSidebar();
		this.createStatus();
		this.createNoSearch();

		// Create the list of images.
		this.createImages();

		this.updateContent();

		this.collection.on( 'add remove reset', this.updateContent, this );
	},

	/**
	 * @return {StockpackBrowser} Returns itself to allow chaining
	 */
	dispose() {
		this.options.selection.off( null, null, this );
		wp.media.View.prototype.dispose.apply( this, arguments );
		return this;
	},

	createImages() {
		this.images = new StockpackImages( {
			controller: this.controller,
			collection: this.collection,
			selection: this.options.selection,
			scrollElement: this.options.scrollElement,
			idealColumnWidth: this.options.idealColumnWidth,
			provider: this.options.provider,
		} );

		this.views.add( this.images );
		this.images.hide();
	},

	createSidebar() {
		const options = this.options,
			selection = options.selection,
			sidebar = this.sidebar = new StockpackSidebar( {
				controller: this.controller,
			} );

		this.views.add( sidebar );

		selection.on( 'selection:single', this.createSingle, this );
		selection.on( 'selection:unsingle', this.disposeSingle, this );

		if ( selection.single() ) {
			this.createSingle();
		}
	},

	createSingle() {
		const sidebar = this.sidebar,
			single = this.options.selection.single();

		sidebar.set( 'details', new StockpackDetails( {
			controller: this.controller,
			model: single,
			priority: 80,
		} ) );

		sidebar.set( 'downloader', new StockpackDownloader( {
			controller: this.controller,
			model: single,
			state: this.model,
			search: this.collection.props.get( 'search' ),
			priority: 90,
		} ) );

		// Show the sidebar on mobile
		if ( this.model.id === 'insert' ) {
			sidebar.$el.addClass( 'visible' );
		}
	},

	disposeSingle() {
		const sidebar = this.sidebar;
		sidebar.unset( 'details' );
		sidebar.unset( 'downloader' );
		// Hide the sidebar on mobile
		sidebar.$el.removeClass( 'visible' );
	},

	updateContent() {
		this.toolbar.get( 'attribution' ).show();
		if ( ! this.collection.length ) {
			this.startLoading();
			this.clearErrors();
			this.dfd = this.collection.more().done( () => {
				if ( ! this.collection.length ) {
					this.displayEmpty();
				} else {
					this.displayImages();
				}
				this.stopLoading();
			} ).fail(
				( message ) => {
					this.displayError( message );

					this.stopLoading();
				},
			);
		} else {
			this.displayImages();
			this.stopLoading();
		}
	},

	createToolbar() {
		const toolbarOptions = {
			controller: this.controller,
		};

		if ( this.controller.isModeActive( 'grid' ) ) {
			toolbarOptions.className = 'media-toolbar wp-filter';
		}

		/**
		 * @member {StockpackToolbar}
		 */
		this.toolbar = new StockpackToolbar( toolbarOptions );

		this.views.add( this.toolbar );

		// this.advancedFilters = new StockpackAdvanced({
		// 	controller: this.controller,
		// 	priority: 50
		// });
		// this.toolbar.set('advanced-filters', this.advancedFilters);

		this.toolbar.set( 'spinner', new wp.media.view.Spinner( {
			priority: -60,
		} ) );
		this.addToolbarFilters();
	},

	addToolbarFilters() {
		// attribution

		this.toolbar.set( 'attribution', new StockpackAttribution( {
			controller: this.controller,
			priority: -70,
		} ).render() );

		// Search is an input, screen reader text needs to be rendered before
		this.toolbar.set( 'searchLabel', new wp.media.view.Label( {
			value: wp.media.view.l10n.stockpack.search,
			attributes: {
				for: 'media-search-input',
			},
			priority: 60,
		} ).render() );
		this.toolbar.set( 'search', new StockpackSearch( {
			controller: this.controller,
			model: this.collection.props,
			priority: 60,
		} ).render() );
		this.createProviderFilter();

		// this.advancedFilters.set('advanced-link', new StockpackAdvancedLink({
		//     controller: this.controller,
		//     model: this.collection.props,
		//     text: wp.media.view.l10n.stockpack.advanced,
		//     priority: -10
		// }).render());

		// this.filtersContainer = new StockpackAdvancedFilters({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: 10
		// });
		// this.advancedFilters.set('filters-container', this.filtersContainer).render();
		//
		// var ImageTypeFilter = new StockpackImageTypeFilter({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -100
		// });
		// this.filtersContainer.set('image-type-filter', ImageTypeFilter.render());
		//
		// var CategoryFilter = new StockpackCategoryFilters({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -95
		// });
		// this.filtersContainer.set('category-filter', CategoryFilter.render());
		//
		// var GenderFilters = new StockpackGenderFilter({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -90
		// });
		// this.filtersContainer.set('gender-filter', GenderFilters.render());
		//
		// var SafeFilter = new StockpackSafeFilters({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -85
		// });
		// this.filtersContainer.set('safe-filter', SafeFilter.render());
		//
		// var OrientationFilter = new StockpackOrientationFilter({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -80
		// });
		// this.filtersContainer.set('orientation-filter', OrientationFilter.render());
		//
		// var ColorFilter = new StockpackColorFilter({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: -70
		// });
		// this.filtersContainer.set('color-filter', ColorFilter.render());
		//
		//
		// this.filtersContainer.set('update-filter', new StockpackFilterButton({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: 20,
		// 	text: wp.media.view.l10n.stockpack.search,
		// }));
		//
		// this.filtersContainer.set('close-advanced-filter', new StockpackFilterClose({
		// 	controller: this.controller,
		// 	model: this.collection.props,
		// 	priority: 10,
		// 	text: ' ',
		// }));
	},

	createProviderFilter() {
		this.toolbar.set( 'provider', new StockpackProviderFilters( {
			controller: this.controller,
			model: this.collection.props,
			priority: 70,
		} ).render() );

		if ( this.toolbar.get( 'provider' ).hidden ) {
			this.toolbar.$el.addClass( 'provider-hidden' );
		}
	},

	createStatus() {
		const statusOptions = {
			controller: this.controller,
			message: wp.media.view.l10n.stockpack.noMedia,
			retry: wp.media.view.l10n.stockpack.retry,
			browser: this,
		};

		if ( this.controller.isModeActive( 'grid' ) ) {
			statusOptions.className = 'media-toolbar wp-filter';
		}

		/**
		 * @member {wp.media.view.Toolbar}
		 */
		this.status = new StockpackEmpty( statusOptions );

		this.status.hide();

		this.views.add( this.status );
	},

	createNoSearch() {
		const statusOptions = {
			controller: this.controller,
			message: wp.media.view.l10n.stockpack.noSearch,
			inspiration: wp.media.view.l10n.stockpack.inspiration,

		};

		/**
		 * @member {wp.media.view.Toolbar}
		 */
		this.noSearch = new StockpackNoSearch( statusOptions );

		this.noSearch.hide();

		this.views.add( this.noSearch );
	},

	displayEmpty( hasError ) {
		if ( hasError ) {
			this.noSearch.hide();
			this.images.hide();
			this.status.show();
		} else {
			this.clearErrors();
			this.images.hide();
			if ( this.collection.props.get( 'search' ) ) {
				this.noSearch.hide();
				this.status.show();
			} else {
				this.noSearch.show();
				this.toolbar.get( 'attribution' ).hide();
				this.status.hide();
			}
		}
	},

	displayImages() {
		this.clearErrors();
		this.status.hide();
		this.noSearch.hide();
		this.images.show();
	},

	displayError( error ) {
		this.displayEmpty( true );
		if(wp.media.view.l10n.stockpack.error[error.code]){
			this.status.model.set( 'message', wp.media.view.l10n.stockpack.error[error.code] );
		}else{
			this.status.model.set( 'message', wp.media.view.l10n.stockpack.error.default );
		}
		this.status.model.set( 'error', error.message );
		if ( error.code === 2 ) {
			this.status.model.set( 'link', {
				url: wp.media.view.settings.stockpack.settings_url,
				text: wp.media.view.l10n.stockpack.link,
			} );
		}
	},

	clearErrors() {
		this.status.model.set( 'message', wp.media.view.l10n.stockpack.noMedia );
		this.status.model.set( 'error', '' );
	},

	stopLoading() {
		this.toolbar.get( 'spinner' ).hide();
	},
	startLoading() {
		this.toolbar.get( 'spinner' ).show();
	},

} );

export default StockpackBrowser;
