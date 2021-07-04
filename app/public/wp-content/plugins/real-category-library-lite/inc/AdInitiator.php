<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\AbstractInitiator;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage;
use DevOwl\RealCategoryLibrary\base\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Initiate real-utils functionality.
 */
class AdInitiator extends \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\AbstractInitiator {
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
        return $this->isPro()
            ? 'https://devowl.io/go/codecanyon/real-category-management/rate'
            : 'https://devowl.io/go/wordpress-org/real-category-management/rate';
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getKeyFeatures() {
        $isPro = $this->isPro();
        return [
            [
                'image' => $this->getAssetsUrl('full-control.gif'),
                'title' => __('Full control over your categories', RCL_TD),
                'description' => __(
                    'Do you maintain many categories? The default view of the categories can be a bit confusing, can’t it? This plugin creates an explorer-like tree on the left side of your posts table. Create, rename, delete or rearrange your categories with a single click!',
                    RCL_TD
                ),
                'available_in' => $isPro
                    ? null
                    : [
                        ['Lite', \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage::COLOR_BADGE_LITE],
                        ['Pro', \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage::COLOR_BADGE_PRO]
                    ],
                'highlight_badge' => $isPro
                    ? null
                    : [
                        'Pro',
                        \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage::COLOR_BADGE_PRO,
                        __('In the PRO version you can also enable the tree on pages and custom post types.', RCL_TD)
                    ]
            ],
            [
                'image' => $this->getAssetsUrl('feature-woocommerce.gif'),
                'title' => __('Supports WooCommerce products', RCL_TD),
                'description' => __(
                    'Shop managers with many products also have to maintain numerous categories. Wouldn\'t it be helpful to maintain the product categories and attributes more elegantly?',
                    RCL_TD
                ),
                'available_in' => $isPro
                    ? null
                    : [['Pro', \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage::COLOR_BADGE_PRO]]
            ],
            [
                'image' => $this->getAssetsUrl('feature-pagination.gif'),
                'title' => __('Switch pages without page reload', RCL_TD),
                'description' => __(
                    'Switching between pages or categories can usually take some time. With this plugin you can enjoy a fast loading of your tables.',
                    RCL_TD
                ),
                'available_in' => $isPro
                    ? null
                    : [['Pro', \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\WelcomePage::COLOR_BADGE_PRO]]
            ]
        ];
    }
    /**
     * Documented in AbstractInitiator.
     *
     * @codeCoverageIgnore
     */
    public function getHeroButton() {
        return [
            __('Go to settings', RCL_TD),
            \DevOwl\RealCategoryLibrary\Core::getInstance()
                ->getConfigPage()
                ->getUrl()
        ];
    }
}
