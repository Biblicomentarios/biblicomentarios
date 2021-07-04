<?php
/**
 * Checkbox Control.
 *
 * @package lazyblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * LazyBlocks_Control_Checkbox class.
 */
class LazyBlocks_Control_Checkbox extends LazyBlocks_Control {
    /**
     * Constructor
     */
    public function __construct() {
        $this->name         = 'checkbox';
        $this->icon         = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17.99 9L16.58 7.58L9.99 14.17L7.41 11.6L5.99 13.01L9.99 17L17.99 9Z" fill="currentColor"/></svg>';
        $this->type         = 'boolean';
        $this->label        = __( 'Checkbox', 'lazy-blocks' );
        $this->category     = 'choice';
        $this->restrictions = array(
            'default_settings' => false,
        );
        $this->attributes   = array(
            'checked'        => 'false',
            'alongside_text' => '',
        );

        // Filters.
        add_filter( 'lzb/prepare_block_attribute', array( $this, 'filter_lzb_prepare_block_attribute' ), 10, 2 );

        parent::__construct();
    }

    /**
     * Register assets action.
     */
    public function register_assets() {
        wp_register_script(
            'lazyblocks-control-checkbox',
            lazyblocks()->plugin_url() . 'controls/checkbox/script.min.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' ),
            '2.3.1',
            true
        );
    }

    /**
     * Get script dependencies.
     *
     * @return array script dependencies.
     */
    public function get_script_depends() {
        return array( 'lazyblocks-control-checkbox' );
    }

    /**
     * Filter block attribute.
     *
     * @param string $attribute_data - attribute data.
     * @param mixed  $control - control data.
     *
     * @return array filtered attribute data.
     */
    public function filter_lzb_prepare_block_attribute( $attribute_data, $control ) {
        if (
            ! $control ||
            ! isset( $control['type'] ) ||
            $this->name !== $control['type'] ||
            ! isset( $control['checked'] )
        ) {
            return $attribute_data;
        }

        if ( ! isset( $attribute_data['default'] ) || ! is_bool( $attribute_data['default'] ) ) {
            $attribute_data['default'] = 'true' === $control['checked'];
        }

        return $attribute_data;
    }
}

new LazyBlocks_Control_Checkbox();
