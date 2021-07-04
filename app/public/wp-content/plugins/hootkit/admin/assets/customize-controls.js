/**
 * Theme Customizer
 */


( function( api ) {

	api.bind('ready', function () {
		api.control('hktb_content_stretch', function (control) {
			control.setting.bind(function (value) {
				switch (value) {
					case 'grid':
						api.control( 'hktb_content_nopad', function( control ) { control.deactivate(); });
						break;
					case 'stretch':
						api.control( 'hktb_content_nopad', function( control ) { control.activate(); });
						break;
				}
			});
		});
	});

} )( wp.customize );