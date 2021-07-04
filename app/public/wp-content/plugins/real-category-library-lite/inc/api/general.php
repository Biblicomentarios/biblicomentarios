<?php

if (!\function_exists('wp_rcl_active')) {
    /**
     * Checks if RCL is active for the current user.
     *
     * @return boolean
     * @since 3.1.0
     */
    function wp_rcl_active() {
        /**
         * Checks if RC is active for the current user. Do not use this filter
         * yourself, instead use wp_rcl_active() function!
         *
         * @param {boolean} True for activated and false for deactivated
         * @return {boolean}
         * @hook RCL/Active
         * @since 3.1.0
         */
        $result = \apply_filters('RCL/Active', \current_user_can('manage_categories'));
        return $result;
    }
}
