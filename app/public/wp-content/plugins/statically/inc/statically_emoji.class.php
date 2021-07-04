<?php

/**
 * Statically_Emoji
 *
 * @since 0.1.0
 */

class Statically_Emoji
{
    const CDN = 'https://cdn.statically.io/twemoji/';

    public static function hook() {
        $options = Statically::get_options();

        if ( 1 !== $options['emoji'] ) {
            return;
        }

        add_filter( 'emoji_svg_url', [ __CLASS__, 'cdn_url_emoji' ], 999 );
        add_filter( 'emoji_url', [ __CLASS__, 'cdn_url_emoji' ], 999 );
        add_filter( 'script_loader_src', [ __CLASS__, 'cdn_url_emoji_release_js' ], 10, 2 );
    }

    /**
     * set new emoji CDN URL
     * 
     * @since 0.1.0
     */
    public static function cdn_url_emoji() {
        return self::CDN;
    }

    /**
     * set new wp-emoji-release.min.js CDN URL
     * 
     * @since 0.1.0
     */
    public static function cdn_url_emoji_release_js( $src, $name ) {
        global $wp_version;

        if ( 'concatemoji' == $name ) {
            $src = Statically::WPCDN . "c/$wp_version/wp-includes/js/wp-emoji-release.min.js";
        }

        return $src;
    }
}