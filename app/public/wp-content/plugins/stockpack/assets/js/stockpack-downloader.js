import StockpackDialog from './stockpack-dialog';

/** @global ShuttepressQueue */

const StockpackDownloader = wp.media.View.extend( {
	tagName: 'div',
	className: 'stockpack-downloader',
	template: wp.template( 'stockpack-downloader' ),
	file: null,
	dialog: null,
	canLicense: false,
	/**
	 * Bind drag'n'drop events to callbacks.
	 */
	initialize() {
		this.options = _.defaults( this.options, {
			state: false,
		} );

		this.on( 'upload-completed', function( attachment ) {
			_.each( [ 'file', 'loaded', 'size', 'percent' ], ( key ) => {
				this.file.attachment.unset( key );
			} );

			this.file.attachment.set( _.extend( attachment, { uploading: false } ) );
			wp.media.model.Attachment.get( attachment.id, this.file.attachment );

			const complete = window.StockpackQueue.all( function( queueAttachment ) {
				return ! queueAttachment.get( 'uploading' );
			} );

			if ( complete ) {
				window.StockpackQueue.reset();
			}
		} );

		this.controller.on( 'download-file', this.downloadTrigger, this );
		this.controller.on( 'download-quick-file', this.downloadQuickTrigger, this );

		return this;
	},

	events: {
		'click .download-stockpack-image': 'download',
		'click .license-stockpack-image': 'showLicense',
	},

	prepare() {
		const data = {
			download: wp.media.view.l10n.stockpack.download,
			license: wp.media.view.l10n.stockpack.licenseAction,
			alreadyLicensed: wp.media.view.l10n.stockpack.alreadyLicensed,
			model: this.model.attributes,
		};

		if(wp.media.view.l10n.stockpack.attribution[ this.getProvider() ].warning){
			data.warning = wp.media.view.l10n.stockpack.attribution[ this.getProvider() ].warning
		}

		if ( wp.media.view.settings.stockpack.filename_change === "yes" ) {
			data.filename_change = true;
			data.filename_placeholder = wp.media.view.l10n.stockpack.filename.placeholder;
			data.desired_filename = "";
		}

		return data;
	},

	setLicenseCost() {
		const options = options || {};
		options.context = this;
		options.data = _.extend( options.data || {}, {
			action: 'license_cost-stockpack',
			security: wp.media.view.settings.stockpack.nonce_license_cost,
			media_id: this.model.id,
			provider: this.getProvider()
		} );
		wp.media.ajax( options ).done( ( response ) => {
			this.dialog.setStatus( response.cost_message );
			if ( response.can_license ) {
				this.canLicense = true;
				jQuery( '.license-button-in-dialog' ).attr( 'disabled', false ).removeClass( 'ui-button-disabled ui-state-disabled' );
			} else {
				const extra = this.model.get( 'extra' );
				this.dialog.setExternalUrl( extra.details_url );
			}
		} ).fail(
			function( error ) {
				this.dialog.setStatus( error[ 0 ].message );
				jQuery( '.license-button-in-dialog' ).attr( 'disabled', true );
			},
		);
	},

	showLicense() {
		this.dialog = new StockpackDialog( {
				...wp.media.view.l10n.stockpack.licensePopup[ this.getProvider() ],
				...{ id: 'license-' + this.getProvider() },
			},
		);
		this.setLicenseCost();
		this.dialog.buttons( [
			{
				text: wp.media.view.l10n.stockpack.licensePopup.cancel,
				click: () => {
					this.dialog.close();
				},
			},
			{
				text: wp.media.view.l10n.stockpack.licensePopup.proceed,
				click: () => {
					this.licenseAndDownload();
				},
				class: 'button-primary license-button-in-dialog',
				disabled: true,
			},
		] );

		this.dialog.open();
	},

	licenseAndDownload() {
		// we call the download function again
		if ( this.canLicense ) {
			this.addFileToQueue( 1 );
			this.dialog.close();
		} else {
			alert( wp.media.view.l10n.stockpack.licensePopup.checkInProgress );
		}
	},

	showTerms() {
		this.dialog = new StockpackDialog( {
				...wp.media.view.l10n.stockpack.terms[ this.getProvider() ],
				...{ id: 'terms-' + this.getProvider() },
			},
		);
		this.dialog.buttons( [
			{
				text: wp.media.view.l10n.stockpack.terms.cancel,
				click: () => {
					this.dialog.close();
				},
			},
			{
				text: wp.media.view.l10n.stockpack.terms.agree,
				click: () => {
					this.acceptTerms();
					// we call the download function again
					this.download();
					this.dialog.close();
				},
				class: 'button-primary',
			},
		] );

		this.dialog.open();
	},

	download() {
		if ( ! this.termsAccepted() ) {
			this.showTerms();
		} else {
			this.addFileToQueue();
		}
	},

	acceptTerms() {
		wp.media.view.settings.stockpack.terms[ this.getProvider() ] = true;
		const options = options || {};
		options.context = this;
		options.data = _.extend( options.data || {}, {
			action: 'terms-stockpack',
			security: wp.media.view.settings.stockpack.nonce_terms,
			provider: this.getProvider(),
		} );
		return wp.media.ajax( options ).done( function() {
			// console.log( 'Terms acceptance stored' );
		} );
	},

	termsAccepted() {
		return wp.media.view.settings.stockpack.terms[ this.getProvider() ];
	},

	getProvider() {
		return this.model.get( 'provider' );
	},

	downloadTrigger( model, quickDownload = false ) {
		if ( this.model === model ) {
			this.addFileToQueue( false, quickDownload );
		}
	},

	downloadQuickTrigger( model ) {
		this.downloadTrigger( model, true );
	},

	openMediaTab() {
		const state = this.options.state;
		if ( 'stockpack' === this.controller.content.mode() ) {
			this.controller.content.mode( 'browse' );
		}
		state.get( 'selection' ).add( this.file.attachment );
		this.controller.trigger( 'library:selection:add' );
	},

	addFileToQueue( mustLicense = false, quickDownload = false ) {
		if ( !mustLicense ) {
			mustLicense = 0;
		}

		// Generate attributes for a new `Attachment` model.
		const attributes = _.extend( {
			item: this.model,
			uploading: true,
			date: new Date(),
			menuOrder: 0,
			progress: 0,
			filename: '',
			uploadedTo: wp.media.model.settings.post.id,
		} );

		this.file = {
			stockpackId: this.model.id,
			mustLicense,
			attachment: new wp.media.model.Attachment( attributes ),
		};
		window.StockpackQueue.add( this.file.attachment );
		if ( ! quickDownload ) {
		this.openMediaTab();
		}else{
			this.downloadStarted();
		}
		this.getFile( mustLicense );
	},

	getFile( mustLicense ) {
		const options = options || {};
		options.context = this;
		options.data = _.extend( options.data || {}, {
			action: 'cache-stockpack',
			media_id: this.file.stockpackId,
			provider: this.getProvider(),
			security: wp.media.view.settings.stockpack.nonce_cache,
			post_id: wp.media.model.settings.post.id,
		} );

		if ( mustLicense !== 0 ) {
			// no cache for must license
			this.file.attachment.set( 'percent', 30 );
			this.downloadFile( mustLicense );
		} else {
			// try cache first
			return wp.media.ajax( options ).done( function( response ) {
				this.trigger( 'upload-completed', response );
			} ).fail( function() {
				// set percent to half
				this.file.attachment.set( 'percent', 30 );
				this.downloadFile( mustLicense );
			} );
		}
	},

	downloadFile( mustLicense ) {
		const options = options || {};
		if ( mustLicense === undefined ) {
			mustLicense = 0;
		}
		let $filenameInput = this.$el.find( '.filename-change input' );
		let overwrite_flename = null;
		if ( $filenameInput ) {
			overwrite_flename = $filenameInput.val();
		}
		options.context = this;
		options.data = _.extend( options.data || {}, {
			action: 'download-stockpack',
			new_filename: overwrite_flename,
			media_id: this.file.stockpackId,
			must_license: this.file.mustLicense,
			provider: this.getProvider(),
			security: wp.media.view.settings.stockpack.nonce_download,
			search_key: this.options.search,
			post_id: wp.media.model.settings.post.id,
			description: this.model.get( 'description' ),
		} );
		const t = this;
		const loading = setInterval( () => {
			t.incrementPercent();
		}, 1000 );
		wp.media.ajax( options ).done( function( response ) {
			clearInterval( loading );
			this.trigger( 'upload-completed', response );
			if ( mustLicense !== 0 ) {
				const extra = t.model.get( 'extra' );
				extra.licensed = true;
				t.model.set( 'extra', extra );
			}
		} ).fail( function( response ) {
			clearInterval( loading );
			this.error( response.message, null, this.file );
		} );
	},

	incrementPercent() {
		if ( this.file.attachment.get( 'percent' ) < 99 ) {
			this.file.attachment.set( {
				percent: this.file.attachment.get( 'percent' ) + 4,
			} );
		}
	},

	downloadStarted() {
		const state = this.options.state;
		const t = this;
		state.get( 'selection' ).add( this.file.attachment );
		this.controller.trigger( 'library:selection:add' );
		this.model.set( 'quickdownloadstarted', true );
		setTimeout( () => {
			this.model.set( 'quickdownloadstarted', false );
		}, 3000 );
	},

	/**
	 * Custom error callback.
	 *
	 * Add a new error to the errors collection, so other modules can track
	 * and display errors. @see wp.Uploader.errors.
	 *
	 * @param  {string}        message
	 * @param  {Object}        data
	 * @param  {plupload.File} file     File that was uploaded.
	 */
	error( message, data, file ) {
		if ( file.attachment ) {
			file.attachment.destroy();
		}

		wp.Uploader.errors.unshift( {
			message: message || pluploadL10n.default_error,
			data,
			file,
		} );
	},

} );

export default StockpackDownloader;
