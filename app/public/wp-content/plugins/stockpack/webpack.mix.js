const mix = require('laravel-mix');

mix.js('assets/js/stockpack-load-frontend.js', 'dist/js')
	.js('assets/js/stockpack-load-admin.js', 'dist/js')
	.js('assets/js/stockpack-settings.js', 'dist/js')
    .sass('assets/sass/stockpack-old-admin.scss', 'dist/css')
    .sass('assets/sass/stockpack-frontend.scss', 'dist/css')
    .sass('assets/sass/stockpack-settings.scss', 'dist/css')
    .sass('assets/sass/stockpack-mlo-compatibility.scss', 'dist/css')
    .sass('assets/sass/stockpack.scss', 'dist/css');


if (!mix.config.production) {
    mix.sourceMaps();
}
