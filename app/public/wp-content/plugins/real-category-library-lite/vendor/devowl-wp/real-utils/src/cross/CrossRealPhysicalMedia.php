<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\cross;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\Core;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Cross-selling for Real Physical Media.
 */
class CrossRealPhysicalMedia extends \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\cross\AbstractCrossSelling {
    const SLUG = 'real-physical-media';
    const PRO_LINK = 'https://devowl.io/go/real-physical-media?source=cross-rpm';
    const FILE_PRO = 'real-physical-media/index.php';
    /**
     * Documented in AbstractCrossSelling.
     *
     * @codeCoverageIgnore
     */
    public function getSlug() {
        return self::SLUG;
    }
    /**
     * Documented in AbstractCrossSelling.
     */
    public function skip() {
        $handler = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealUtils\Core::getInstance()->getCrossSellingHandler();
        return $handler->isInstalled(self::FILE_PRO);
    }
    /**
     * Documented in AbstractCrossSelling.
     *
     * @codeCoverageIgnore
     */
    public function getMeta() {
        return [
            // Upload in "Add new" and Grid mode
            'upload' => [
                'title' => __('Did you know that?', REAL_UTILS_TD),
                'image' => $this->getAssetsUrl('feature-manual.jpg'),
                'description' => __(
                    'Your uploads can say more than a thousand words. Unfortunately, search engines do not understand the content of e.g. images. Organize your uploads directly in meaningful folder names and get a better ranking in search engines!',
                    REAL_UTILS_TD
                ),
                'link' => self::PRO_LINK
            ],
            'move' => [
                'title' => __('Did you know that?', REAL_UTILS_TD),
                'image' => $this->getAssetsUrl('feature-queue.gif'),
                'description' => __(
                    'Your uploads can say more than a thousand words. Unfortunately, search engines do not understand the content of e.g. images. Organize your uploads directly in meaningful folder names and get a better ranking in search engines!',
                    REAL_UTILS_TD
                ),
                'link' => self::PRO_LINK
            ]
        ];
    }
}
