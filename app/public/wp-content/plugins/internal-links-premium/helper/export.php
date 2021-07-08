<?php
namespace ILJ\Helper;

use ILJ\Type\KeywordList;

/**
 * Export toolset
 *
 * Methods for data export
 *
 * @since   1.2.0
 * @package ILJ\Helper
 */
class Export
{
    const ILJ_EXPORT_CSV_FORMAT_HEADLINE = '"%1$s";"%2$s";"%3$s";"%4$s";"%5$s"';
    const ILJ_EXPORT_CSV_FORMAT_LINE = '"%1$d";"%2$s";"%3$s";"%4$s";"%5$s"';

    /**
     * Prints the headline for keyword export as CSV
     *
     * @since  1.2.0
     * @param  bool $verbose Permits echo of headline output if true
     * @return string
     */
    public static function printCsvHeadline($verbose=false)
    {
        $headline = sprintf(self::ILJ_EXPORT_CSV_FORMAT_HEADLINE, "ID", "Type", "Keywords (ILJ)", "Title", "Url");

        if (!$verbose) {
            echo $headline;
        }

        return $headline;
    }

    /**
     * Converts all index relevant posts to CSV data
     *
     * @since  1.2.0
     * @param  bool $empty   Flag for output of empty entries
     * @param  bool $verbose Permits echo of CSV output if true
     * @return string
     */
    public static function printCsvPosts($empty, $verbose=false)
    {
        $csv = '';
        $posts = IndexAsset::getPosts();

        foreach ($posts as $post) {
            $keyword_list = KeywordList::fromMeta($post->ID, 'post');

            if ($empty && !$keyword_list->getCount()) {
                continue;
            }

            $csv_curr = PHP_EOL;
            $csv_curr .= sprintf(self::ILJ_EXPORT_CSV_FORMAT_LINE, $post->ID, 'post', $keyword_list->encoded(false), $post->post_title, get_permalink($post->ID));

            if (!$verbose) {
                echo $csv_curr;
            }

            $csv .= $csv_curr;
        }
        return $csv;
    }

    /**
     * Prints out all index relevant terms as CSV line
     *
     * @since  1.2.0
     * @param  bool $empty   Flag for output of empty entries
     * @param  bool $verbose Permits echo of CSV output if true
     * @return string
     */
    public static function printCsvTerms__premium_only($empty, $verbose=false)
    {
        $csv = '';
        $terms = IndexAsset::getTerms__premium_only();

        foreach($terms as $term) {
            $keyword_list = KeywordList::fromMeta($term->term_id, 'term');

            if ($empty && !$keyword_list->getCount()) {
                continue;
            }

            $csv_curr = PHP_EOL;
            $csv_curr .= sprintf(self::ILJ_EXPORT_CSV_FORMAT_LINE, $term->term_id, 'term', $keyword_list->encoded(), $term->name, get_term_link($term->term_id));

            if (!$verbose) {
                echo $csv_curr;
            }

            $csv .= $csv_curr;
        }
        return $csv;
    }

    /**
     * Prints out all custom links as CSV line
     *
     * @since  1.2.0
     * @param  bool $empty   Flag for output of empty entries
     * @param  bool $verbose Permits echo of CSV output if true
     * @return string
     */
    public static function printCsvCustomLinks__premium_only($empty, $verbose=false)
    {
        $csv = '';
        $custom_links = IndexAsset::getCustomLinks__premium_only();

        foreach($custom_links as $custom_link) {
            $keyword_list = KeywordList::fromMeta($custom_link->ID, 'custom');

            if ($empty && !$keyword_list->getCount()) {
                continue;
            }

            $custom_link_url = get_post_meta(
                $custom_link->ID,
                \ILJ\Posttypes\CustomLinks::ILJ_FIELD_CUSTOM_LINKS_URL,
                true
            );

            $csv_curr = PHP_EOL;
            $csv_curr .= sprintf(self::ILJ_EXPORT_CSV_FORMAT_LINE, $custom_link->ID, 'custom', $keyword_list->encoded(), $custom_link->post_title, $custom_link_url);

            if (!$verbose) {
                echo $csv_curr;
            }

            $csv .= $csv_curr;
        }
        return $csv;
    }
}