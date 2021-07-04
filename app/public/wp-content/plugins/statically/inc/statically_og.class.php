<?php

/**
 * Statically OG
 *
 * @since 0.4.0
 */

class Statically_OG
{
    const CDN = 'https://cdn.statically.io/og';

    public static function hook() {
        $options = Statically::get_options();

        if ( 1 !== $options['og'] ) {
            return;
        }

        self::generate_og();
    }

    /**
     * generate OG image
     * 
     * @since 0.4.0
     */
    public static function generate_og() {
        $options = Statically::get_options();

        if ( ! has_post_thumbnail() ) {
            $text = get_the_title();
            if ( is_home() ) {
                $text = get_bloginfo( 'name' );
            }
            if ( 0 === strlen( $text ) ) {
                return;
            }

            // start options
            $params = '';
            if ( 'light' !== $options['og_theme']  ) {
                $params = '/theme=' . $options['og_theme'];
            }

            // define font size for OG Image Font Size option
            $font_lg = '120px';
            $font_xl = '150px';
            if ( 'large' === $options['og_fontsize'] ) {
                $fontsize = $font_lg;
            }
            if ( 'extra-large' === $options['og_fontsize'] ) {
                $fontsize = $font_xl;
            }

            // font size
            if ( 'medium' !== $options['og_fontsize'] ) {
                $params .= ',fontSize=' . $fontsize;
            }

            // image type
            $type = '.jpg';
            if ( 'png' === $options['og_type'] ) {
                $type = '.png';
            }

            // clean up params if the theme is light by finding and remove
            // the first comma `,` from options and change it to slash `/`
            if ( 'light' === $options['og_theme'] ) {
                $params = substr($params, strpos($params, ',') + 1);
                if ( 'medium' !== $options['og_fontsize'] ) {
                    $params = '/' . $params;
                }
            }

            // end options
            $params .= '/';

            // combine URL
            $url = self::CDN . $params;
            $url = $url . rawurlencode( $text ) . $type;

            // this is where the OG Image service shows
            $og = '<meta property="og:image" content="' . $url . '" />' . "\n";
            $og .= '<meta property="og:image:secure_url" content="' . $url . '" />' . "\n";
            $og .= '<meta name="twitter:image" content="' . $url .'" />' . "\n";

            echo $og;
        }
    }
}
