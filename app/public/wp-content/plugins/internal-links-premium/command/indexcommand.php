<?php
namespace ILJ\Command;

use ILJ\Core\IndexBuilder;
use ILJ\Database\Linkindex;
use ILJ\Backend\Environment;
use ILJ\Backend\IndexRebuildNotifier;

if (class_exists('WP_CLI_Command')) {
    /**
     * Manage the linkindex through CLI
     *
     * @since 1.2.10
     */
    class IndexCommand extends \WP_CLI_Command
    {
        /**
         * (Re-)build the index
         *
         * ## EXAMPLES
         *     # Build / rebuild the index
         *     $ wp ilj index build
         */
        public function build($args, $assoc_args)
        {
            $index_builder = new IndexBuilder();
            $index_stat    = $index_builder->buildIndex();
            IndexRebuildNotifier::unsetNotifier();
            \WP_CLI::success("Index built successfully. Built " . $index_stat['last_update']['entries'] . " records in " . $index_stat['last_update']['duration'] . " seconds.");
            return;
        }

        /**
         * Flush the whole index (be careful with that in production mode!)
         *
         * ## EXAMPLES
         *     # Flush the whole index tables
         *     $ wp ilj index flush
         */
        public function flush($args, $assoc_args)
        {
            \WP_CLI::confirm("Are you shure to flush the whole index?", $assoc_args);
            Linkindex::flush();

            $feedback = [
	            "last_update" => [
		            "date"     => new \DateTime(),
		            "entries"  => 0,
		            "duration" => null
	            ]
            ];
            Environment::update('linkindex', $feedback);

            \WP_CLI::success("Index flushed successfully.");
            return;
        }
    }
}
