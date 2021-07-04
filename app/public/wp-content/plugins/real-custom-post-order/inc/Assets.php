<?php

namespace DevOwl\RealCustomPostOrder;

use DevOwl\RealCustomPostOrder\base\UtilsProvider;
use DevOwl\RealCustomPostOrder\sortable\AbstractSortable;
use DevOwl\RealCustomPostOrder\sortable\PostSortable;
use DevOwl\RealCustomPostOrder\Vendor\MatthiasWeb\Utils\Assets as UtilsAssets;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Asset management for frontend scripts and styles.
 */
class Assets {
    use UtilsProvider;
    use UtilsAssets;
    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from src/public/lib, too.
     *
     * @param string $type The type (see utils Assets constants)
     * @param string $hook_suffix The current admin page
     */
    public function enqueue_scripts_and_styles($type, $hook_suffix = null) {
        // Generally check if an entrypoint should be loaded
        if (!\count(\DevOwl\RealCustomPostOrder\sortable\AbstractSortable::someAvailable())) {
            return;
        }
        $realUtils = RCPO_ROOT_SLUG . '-real-utils-helper';
        // Your assets implementation here... See utils Assets for enqueue* methods
        // $useNonMinifiedSources = $this->useNonMinifiedSources(); // Use this variable if you need to differ between minified or non minified sources
        // Our utils package relies on jQuery, but this shouldn't be a problem as the most themes still use jQuery (might be replaced with https://github.com/github/fetch)
        // Enqueue external utils package
        $scriptDeps = $this->enqueueUtils();
        $scriptDeps = \array_merge($scriptDeps, [$realUtils, 'jquery-ui-core', 'jquery-ui-sortable', 'wp-pointer']);
        // Enqueue plugin entry points
        $handle = $this->enqueueScript('admin', 'admin.js', $scriptDeps);
        $this->enqueueStyle('admin', 'admin.css', [$realUtils]);
        // Localize script with server-side variables
        wp_localize_script($handle, RCPO_SLUG_CAMELCASE, $this->localizeScript($type));
    }
    /**
     * Localize the WordPress backend and frontend. If you want to provide URLs to the
     * frontend you have to consider that some JS libraries do not support umlauts
     * in their URI builder. For this you can use utils Assets#getAsciiUrl.
     *
     * Also, if you want to use the options typed in your frontend you should
     * adjust the following file too: src/public/ts/store/option.tsx
     *
     * @param string $context
     * @return array
     */
    public function overrideLocalizeScript($context) {
        $active = \DevOwl\RealCustomPostOrder\sortable\AbstractSortable::someAvailable();
        $sortables = [];
        $typeOptions = [];
        foreach ($active as $obj) {
            $isSortable = $obj->isSortable();
            $sortables[$obj->getType()] = $isSortable;
            if ($isSortable) {
                $typeOptions[$obj->getType()] = $obj->localize();
            }
        }
        return ['sortables' => $sortables, 'types' => $typeOptions];
    }
}
