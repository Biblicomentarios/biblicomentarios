<?php
namespace ILJ\Command;

use ILJ\Helper\Export;

if (class_exists('WP_CLI_Command')) {
    /**
     * Tools for exporting settings and keyword configurations.
     *
     * @since 1.2.10
     */
    class ExportCommand extends \WP_CLI_Command
    {
        /**
         * Export plugin settings to a JSON file
         *
         * [--output=<path>]
         * : The path where export gets stored
         * ---
         * default: .
         * ---
         *
         * ## EXAMPLES
         *     # Export settings to file
         *     $ wp ilj export settings --path=.
         */
        public function settings($args, $assoc_args)
        {
            $output_file = self::getPathArgument($assoc_args['output']) . 'ilj_settings.json';

            file_put_contents($output_file, \json_encode(\ILJ\Core\Options::exportOptions(), JSON_UNESCAPED_SLASHES));

            self::exportSuccess($output_file);
            return;
        }

        /**
         * Export keyword configurations to a CSV file
         *
         * [--output=<path>]
         * : The path where export gets stored
         * ---
         * default: .
         * ---
         *
         * [--empty]
         * : For keywords export: Export also assets, that do not have configured keywords
         *
         * ## EXAMPLES
         *     # Export keyword configurations to file
         *     $ wp ilj export keywords --path=. --empty
         */
        public function keywords($args, $assoc_args)
        {
            $output_file = self::getPathArgument($assoc_args['output']) . 'ilj_keywords_cli.csv';
            $option_empty = isset($assoc_args['empty']) && $assoc_args['empty'] ? false : true;

            $csv = [
	            Export::printCsvHeadline(true),
	            Export::printCsvPosts($option_empty, true),
	            Export::printCsvTerms__premium_only($option_empty, true),
	            Export::printCsvCustomLinks__premium_only($option_empty, true)
            ];

            file_put_contents($output_file, implode('', $csv));

            self::exportSuccess($output_file);
            return;
        }

        /**
         * Returns an absolute (and valid) path from user input
         *
         * @since  1.2.10
         * @param  string $output The output parameter set through CLI
         * @return string
         */
        protected static function getPathArgument($output)
        {
            if (!is_dir($output)) {
                \WP_CLI::error("The output directory \"" . $output . "\" does not exist.");
            }
            return  realpath($output) . DIRECTORY_SEPARATOR;
        }

        /**
         * Outputs export success message
         *
         * @since  1.2.10
         * @param  string $output_file The file where the export got stored
         * @return void
         */
        protected static function exportSuccess($output_file)
        {
            \WP_CLI::success("The export is stored in \"" . $output_file . "\"");
        }
    }
}
