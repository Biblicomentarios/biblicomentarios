import StockpackImage from './stockpack-image';

const StockpackDetails = StockpackImage.extend( {
	tagName: 'div',
	className: 'attachment-details',
	template: wp.template( 'stockpack-attachment-details' ),

	attributes() {
		return {
			tabIndex: 0,
			'data-id': this.model.get( 'id' ),
		};
	},

	initialize() {
		this.options = _.defaults( this.options, {
			rerenderOnModelChange: false,
		} );

		this.on( 'ready', this.initialFocus );
		// Call 'initialize' directly on the parent class.
		StockpackImage.prototype.initialize.apply( this, arguments );
	},

	initialFocus() {
		if ( ! wp.media.isTouchDevice ) {
			this.$( 'input[type="text"]' ).eq( 0 ).focus();
		}
	},

} );

export default StockpackDetails;
