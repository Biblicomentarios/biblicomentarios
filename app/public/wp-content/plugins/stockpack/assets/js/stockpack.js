import StockpackBrowser from './stockpack-browser';
import StockpackImages from './models/stockpack-images-model';
import StockpackSelection from './models/stockpack-selection-model';
import './stockpack-helpers';

const Stockpack = {
	init: () => {
		const oldMediaFrame = wp.media.view.MediaFrame.Post;

		const oldMediaFrameSelect = wp.media.view.MediaFrame.Select;
		window.StockpackQueue = new StockpackImages( [], { query: false } );
		window.StockpackQueueErrors = new Backbone.Collection();


		// Extending the current media library frame to add a new tab
		wp.media.view.MediaFrame.Post = oldMediaFrame.extend( {

			/**
			 * overwrite router to
			 *
			 * @param {wp.media.view.Router} routerView
			 */
			browseRouter( routerView ) {
				oldMediaFrame.prototype.browseRouter.apply( this, arguments );
				routerView.set( {
					stockpack: {
						text: wp.media.view.l10n.stockpack.title,
						priority: 60,
					},
				} );
			},

			/**
			 * Bind region mode event callbacks.
			 *
			 * @see media.controller.Region.render
			 */
			bindHandlers() {
				oldMediaFrame.prototype.bindHandlers.apply( this, arguments );
				this.on( 'content:create:stockpack', this.stockpackContent, this );
			},

			/**
			 * Render callback for the content region in the `browse` mode.
			 *
			 * @param {wp.media.controller.Region} contentRegion
			 */
			stockpackContent( contentRegion ) {
				const state = this.state();
				if ( ! state.get( 'stockpack-images' ) ) {
					state.set( 'stockpack-images', window.getStockpackQuery() );
					// Watch for downloaded images.
					state.get( 'library' ).observe( window.StockpackQueue );
				}

				if ( ! state.get( 'stockpack-selection' ) ) {
					let props;
					props = state.get( 'stockpack-images' ).props.toJSON();
					props = _.omit( props, 'orderby', 'query' );

					state.set( 'stockpack-selection', new StockpackSelection( null, {
						multiple: false,
						props,
					} ) );
				}

				this.$el.removeClass( 'hide-toolbar' );

				// Browse our library of attachments.
				contentRegion.view = new StockpackBrowser( {
					collection: state.get( 'stockpack-images' ),
					selection: state.get( 'stockpack-selection' ),
					controller: this,
					model: state,
					idealColumnWidth: state.get( 'idealColumnWidth' ),
					suggestedWidth: state.get( 'suggestedWidth' ),
					suggestedHeight: state.get( 'suggestedHeight' ),
				} );
			},

			getFrame( id ) {
				return this.states.findWhere( { id } );
			},

		} );

		// Order is important, post is based on the old select
		wp.media.view.MediaFrame.Select = oldMediaFrameSelect.extend( {

			/**
			 * overwrite router to
			 *
			 * @param {wp.media.view.Router} routerView
			 */
			browseRouter( routerView ) {
				oldMediaFrameSelect.prototype.browseRouter.apply( this, arguments );
				routerView.set( {
					stockpack: {
						text: wp.media.view.l10n.stockpack.title,
						priority: 60,
					},
				} );
			},

			/**
			 * Bind region mode event callbacks.
			 *
			 * @see media.controller.Region.render
			 */
			bindHandlers() {
				oldMediaFrameSelect.prototype.bindHandlers.apply( this, arguments );
				this.on( 'content:create:stockpack', this.stockpackContent, this );
			},

			/**
			 * Render callback for the content region in the `browse` mode.
			 *
			 * @param {wp.media.controller.Region} contentRegion
			 */
			stockpackContent( contentRegion ) {
				const state = this.state();
				if ( ! state.get( 'stockpack-images' ) ) {
					state.set( 'stockpack-images', window.getStockpackQuery() );
					// Watch for downloaded images.
					state.get( 'library' ).observe( window.StockpackQueue );
				}

				if ( ! state.get( 'stockpack-selection' ) ) {
					let props;
					props = state.get( 'stockpack-images' ).props.toJSON();
					props = _.omit( props, 'orderby', 'query' );

					state.set( 'stockpack-selection', new StockpackSelection( null, {
						multiple: false,
						props,
					} ) );
				}

				if ( ! state.get( 'provider' ) ) {
					state.set( 'provider', '' );
				}

				this.$el.removeClass( 'hide-toolbar' );

				// Browse our library of attachments.
				contentRegion.view = new StockpackBrowser( {
					collection: state.get( 'stockpack-images' ),
					selection: state.get( 'stockpack-selection' ),
					controller: this,
					model: state,
					idealColumnWidth: state.get( 'idealColumnWidth' ),
					suggestedWidth: state.get( 'suggestedWidth' ),
					suggestedHeight: state.get( 'suggestedHeight' ),
					provider: state.get( 'provider' ),
				} );
			},

			getFrame( id ) {
				return this.states.findWhere( { id } );
			},

		} );
	}
}

export default Stockpack;
