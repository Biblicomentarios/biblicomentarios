import StockpackImagesModel from './models/stockpack-images-model';

window.getStockpackQuery = function( props ) {
	return new StockpackImagesModel( null, {
		props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } ),
	} );
};
