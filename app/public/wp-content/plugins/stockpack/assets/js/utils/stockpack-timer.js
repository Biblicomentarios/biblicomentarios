export default class StockpackTimer {
	/**
	 * @param {Object} $element
	 */
	constructor( $element ) {
		this.$ = {};
		this.$.element = $element;
		this.timerTimeout = null;
		this.seconds = 0;
		this.timer();
	}

	timer() {
		if ( ! this.$.element.length ) {
			return false;
		}
		if ( ! this.$.element.hasClass( 'parsed' ) ) {
			this.seconds = 0;
			const time = this.$.element.text().split( ':' );
			let increment = 1;
			time.reverse().forEach( ( element ) => {
				this.seconds += increment * element;
				increment = increment * 60;
			} );
			this.$.element.addClass( 'parsed' );
		}

		const days = Math.floor( this.seconds / 24 / 60 / 60 );
		const hoursLeft = Math.floor( ( this.seconds ) - ( days * 86400 ) );
		const hours = Math.floor( hoursLeft / 3600 );
		const minutesLeft = Math.floor( ( hoursLeft ) - ( hours * 3600 ) );
		const minutes = Math.floor( minutesLeft / 60 );
		const remainingSeconds = this.seconds % 60;

		function pad( n ) {
			return ( n < 10 ? '0' + n : n );
		}

		let text = pad( hours ) + ':' + pad( minutes ) + ':' + pad( remainingSeconds );
		if ( days ) {
			text = pad( days ) + ':' + text;
		}
		this.$.element.text( text );
		if ( this.seconds === 0 ) {
			clearTimeout( this.timerTimeout );
			this.$.element.parent().text( wp.media.view.l10n.stockpack.limit.reset );
			return false;
		} else {
			this.seconds--;
		}
		clearTimeout( this.timerTimeout );
		this.timerTimeout = setTimeout(
			() => {
				this.timer();
			}, 1000, this,
		);
	}

	destroy() {
		if ( this.$.el ) {
			this.$.el.remove();
		}
		clearTimeout( this.timerTimeout );
	}
}
