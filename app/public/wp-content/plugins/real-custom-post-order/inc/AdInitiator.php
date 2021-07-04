<?php

namespace DevOwl\RealCustomPostOrder;

use DevOwl\RealCustomPostOrder\Vendor\DevOwl\RealUtils\AbstractInitiator;
use DevOwl\RealCustomPostOrder\base\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Initiate real-utils functionality.
 */
class AdInitiator extends \DevOwl\RealCustomPostOrder\Vendor\DevOwl\RealUtils\AbstractInitiator {
    use UtilsProvider;
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getPluginBase() {
        return $this;
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getPluginAssets() {
        return $this->getCore()->getAssets();
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getRateLink() {
        return 'https://devowl.io/go/wordpress-org/real-custom-post-order/rate';
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getKeyFeatures() {
        return [
            [
                'image' => $this->getAssetsUrl('feature-move.gif'),
                'title' => __('Drag & Drop your posts and pages', RCPO_TD),
                'description' => __(
                    'A custom order of posts – whether it’s a post order, a custom page order, a custom product order, or a custom post type order – can help you organize your content in a more intuitive way that helps you find your content more simply and quickly.',
                    RCPO_TD
                )
            ]
        ];
    }
}
