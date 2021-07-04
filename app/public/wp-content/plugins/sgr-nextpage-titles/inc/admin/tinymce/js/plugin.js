/* global getUserSetting, setUserSetting */
( function( tinymce ) {
// Set the minimum value for the modals z-index higher than #wpadminbar (100000)
if ( ! tinymce.ui.FloatPanel.zIndex || tinymce.ui.FloatPanel.zIndex < 100100 ) {
	tinymce.ui.FloatPanel.zIndex = 100100;
}

tinymce.PluginManager.add( 'multipage', function( editor ) {
	var __ = editor.editorManager.i18n.translate;

	// Register buttons
	editor.addButton( 'subpage', {
		tooltip: __( 'Start a new Subpage' ),
		onclick: function() {
			editor.execCommand( 'MPP_Subpage' );
		}
	});
	
	// Register commands
	editor.addCommand( 'MPP_Subpage', function( tag ) {
		editor.windowManager.open( {
			title: __( 'Enter the subpage title' ),
			body: [{
				type: 'textbox',
				name: 'title',
				label: 'Title',
				value: ''
			}],
			onsubmit: function( e ) {
				// Check for title length
				if (typeof e.data.title != 'undefined' && e.data.title.length)
					shortcode = '[nextpage title="' + e.data.title + '"]<br />';
                       
				editor.execCommand('mceInsertContent', 0, shortcode);
            }
		});
	});
	
	// Replace Read More/Next Page tags with images
	editor.on( 'BeforeSetContent', function( e ) {
		if ( e.content ) {
			if ( e.content.indexOf( '[nextpage title=\"' ) !== -1 ) {
				e.content = e.content.replace( /\[nextpage title=\"(.*?)\"\]/g, function( match, subtitle ) {
					return '<img src="' + tinymce.Env.transparentSrc + '" data-wp-more="subpage" class="wp-more-tag mce-wp-subpage" ' +
						'title="' + subtitle + '" data-mce-resize="false" data-mce-placeholder="1" />';
				});
			}
		}
	});

	// Replace images with tags
	editor.on( 'PostProcess', function( e ) {
		if ( e.get ) {
			e.content = e.content.replace(/<img[^>]+>/g, function( image ) {
				var match, subtitle = '';
					if ( image.indexOf( 'data-wp-more="subpage"' ) !== -1 ) {
					if ( match = image.match( /title="([^"]+)"/ ) ) {
						subtitle = match[1];
					}

					image = '[nextpage title="' + subtitle + '"]';
				}

				return image;
			});
		}
	});
});

}( window.tinymce ));