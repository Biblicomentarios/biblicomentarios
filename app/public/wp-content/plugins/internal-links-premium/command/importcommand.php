<?php
namespace ILJ\Command;

use ILJ\Core\Options;
use ILJ\Core\Options\Whitelist;
use ILJ\Helper\Encoding;
use ILJ\Core\Options as CoreOptions;
use ILJ\Helper\Import;
use ILJ\Helper\Keyword;

if (class_exists('WP_CLI_Command')) {
    /**
     * Tools for importing settings and keyword configurations from an input file.
     *
     * @since 1.2.10
     */
    class ImportCommand extends \WP_CLI_Command
    {
        /**
         * Import plugin settings by a JSON file
         *
         * --file=<path>
         * : path of the input file for import
         *
         * ## EXAMPLES
         *     # Import settings from file
         *     $ wp ilj import settings --file=ilj_settings.json
         */
        public function settings($args, $assoc_args)
        {
            $file = $assoc_args['file'];

            if (!file_exists($file)) {
                \WP_CLI::error("The file \"' . $file . '\" does not exist.");
            }

            $file_content = file_get_contents($file);
            $file_json = Encoding::jsonToArray($file_content);

            if ($file_json === false) {
                \WP_CLI::error("Input file is no valid settings JSON file.");
            }

            $import_count = CoreOptions::importOptions($file_json);

            if ($import_count) {
                \WP_CLI::success("Imported " . $import_count . " settings from \"" . $file . "\".");
                return;
            }

            \WP_CLI::line("Nothing to import.");
            return;
        }

        /**
         * Import keyword configurations by a CSV file
         *
         * --file=<path>
         * : path of the input file for import
         *
         * ## EXAMPLES
         *     # Import keyword configurations from file
         *     $ wp ilj import keywords --file=ilj_keywords.csv
         */
        public function keywords($args, $assoc_args)
        {
            $file = $assoc_args['file'];

            if (!file_exists($file)) {
                \WP_CLI::error("The file \"' . $file . '\" does not exist.");
            }

            $import_count = Keyword::importKeywordsFromFile($file);

            if ($import_count) {
                \WP_CLI::success("Imported keywords for " . $import_count . " assets from \"" . $file . "\".");
                return;
            }

            \WP_CLI::line("Nothing to import.");
            return;
        }

        /**
         * Import keyword configurations by a CSV file
         *
         * ## OPTIONS
         * <type>
         * : Choose on which public type of assets you want to apply the import
         * post        On posts, pages and custom post types
         * term        On categories, tags and custom taxonomies
         *
         * --asset-types=<types>
         * : The types to import from the asset (comma separated)
         *
         * --source=<source>
         * : The source where the keywords get loaded from
         * ---
         * options:
         *  - title
         *  - yoast-seo
         *  - rankmath
         * ---
         *
         * ## EXAMPLES
         *     # Set the title of every post and page as keyword for linking:
         *     $ wp ilj import keywords-intern posts --asset-types=post,page --source=title
         *
         *     # Set the title of category and tag term as keyword for linking:
         *     $ wp ilj import keywords-intern term --asset-types=category,post_tag --source=title
         *
         * @subcommand keywords-intern
         */
        public function keywordsIntern($args, $assoc_args)
        {
            list($type) = $args;

            $asset_types = explode(',', $assoc_args['asset-types']);

            if ($assoc_args['source'] == 'yoast-seo' && !defined('WPSEO_VERSION')) {
                \WP_CLI::error("Not available - Yoast SEO Plugin is not installed or activated on this installation of WordPress.");
            } elseif ($assoc_args['source'] == 'rankmath' && !class_exists('RankMath')) {
                \WP_CLI::error("Not available - RankMath Plugin is not installed or activated on this installation of WordPress.");
            }

            switch($type) {
	            case 'post':
	                $source = 'ilj-import-intern-post-' . $assoc_args['source'];

	                $editor_post_types = array_map(
	                    function ($post_type) {
	                        return $post_type->name;
	                    }, Whitelist::getEditorPostTypes()
	                );

	                foreach($asset_types as $asset_type) {
	                    if (!in_array($asset_type, $editor_post_types)) {
	                        \WP_CLI::error("The asset type \"" . $asset_type . "\" is not allowed");
	                        return;
	                    }
	                }

	                Import::internalPost__premium_only($asset_types, [$source]);
	                break;
	            case 'term':
	                $source = 'ilj-import-intern-term-' . $assoc_args['source'];

	                $editor_taxonomy_types = array_map(
	                    function ($taxonomy_type) {
	                        return $taxonomy_type->name;
	                    }, Options\TaxonomyWhitelist::getTaxonomyTypes()
	                );

	                foreach($asset_types as $asset_type) {
	                    if (!in_array($asset_type, $editor_taxonomy_types)) {
	                         \WP_CLI::error("The asset type \"" . $asset_type . "\" is not allowed");
	                         return;
	                    }
	                }

	                Import::internalTerm__premium_only($asset_types, [$source]);
	                break;
            }

            \WP_CLI::success("Finished importing.");
        }
    }
}