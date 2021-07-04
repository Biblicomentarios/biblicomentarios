<?php

// helper function for featured_image caption
if ( ! function_exists( 'stockpack_featured_image_caption' ) ) {
    function stockpack_featured_image_caption( $attachment_id = null ) {
        $stockpack_settings = StockpackSettings::get_instance();
        if ( $stockpack_settings->get_featured_caption_setting() !== 'yes' ) {
            return '';
        }

        if ( ! $attachment_id ) {
            $attachment_id = get_post_thumbnail_id();
            if ( ! $attachment_id ) {
                return '';
            }
        }

        return stockpack_fetch_caption( $attachment_id );
    }
}

if ( ! function_exists( 'stockpack_add_caption' ) ) {
    function stockpack_add_caption( $html ) {
        return $html . stockpack_featured_image_caption();
    }
}

if ( ! function_exists( 'stockpack_fetch_caption' ) ) {
    function stockpack_fetch_caption( $attachment_id ) {
        $caption = wp_get_attachment_caption( $attachment_id );

        if ( $caption ) {
            return '<figcaption class="stockpack-caption">' . $caption . '</figcaption>';
        }

        return '';
    }
}

if ( ! function_exists( 'stockpack_add_caption_to_featured_image' ) ) {
    function stockpack_add_caption_to_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
        // some themes have support already.
        $my_theme = wp_get_theme();
        $excluded = apply_filters( 'stockpack_excluded_themes_caption', array( 'Bimber' ) );
        if ( in_array( $my_theme->get( 'Name' ), $excluded ) ) {
            return $html;
        }

        return $html . stockpack_featured_image_caption( $post_thumbnail_id );
    }
}

add_filter( 'post_thumbnail_html', 'stockpack_add_caption_to_featured_image', 10, 5 );
