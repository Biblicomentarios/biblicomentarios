<?php

namespace ILJ\Helper;

use ILJ\Database\Postmeta;
use ILJ\Posttypes\CustomLinks;
use ILJ\Type\KeywordList;

/**
 * Toolset for keywords
 *
 * Methods for keyword (-phrases)
 *
 * @package ILJ\Helper
 * @since   1.0.4
 */
class Keyword
{
    /**
     * Calculates an effective word count value with respect to configured gaps
     *
     * @since  1.0.4
     * @param  string $keyword The (keyword-) phrase where words get counted
     * @return int
     */
    public static function gapWordCount($keyword)
    {
        $word_count = count(explode(' ', $keyword));

        preg_match_all('/{(?:1,)?(\d),?}/', $keyword, $matches);

        if (isset($matches[1])) {
            $word_count -= count($matches[1]);

            foreach ($matches[1] as $match) {
                $word_count += (int) $match;
            }
        }

        return $word_count;
    }

    /**
     * Imports keywords for indexable assets from a given CSV file
     *
     * @since 1.2.0
     * @param string $file The path to the CSV file
     *
     * @return int
     */
    public static function importKeywordsFromFile($file)
    {
        ini_set('auto_detect_line_endings', true);
        $import_count = 0;

        if (!file_exists($file)) {
            return $import_count;
        }

        $handle = fopen($file, "r");

        for ($i = 0; $row = fgetcsv($handle, 0, ';'); ++$i) {

            if ($i === 0) {
                continue;
            }

            if (!is_array($row) || count($row) !== 5) {
                continue;
            }

            $allowed_import_types = ['post'];

            if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                $allowed_import_types = array_merge($allowed_import_types, ['term', 'custom']);
            }

            if (!in_array($row[1], $allowed_import_types) || $row[2] == '') {
                continue;
            }

            $id = is_numeric($row[0]) ? (int) $row[0] : null;
            $type = $row[1];
            $keywords = KeywordList::fromInput($row[2]);
            $existing_keywords = KeywordList::fromMeta($id, $type);

            if (! $existing_keywords->hasAdditionalKeys($keywords)) {
                continue;
            }

            $existing_keywords->merge($keywords);

            if ($type == 'post') {
                if (!$id || !get_post($id)) {
                    continue;
                }

                update_post_meta($id, Postmeta::ILJ_META_KEY_LINKDEFINITION, $existing_keywords->getKeywords());
            }

            if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                if ($type != 'post') {
                    if ($type == 'custom') {

                        if (!$id) {
                            $custom_link_title = $row[3];
                            $custom_link_url = esc_url($row[4]);
                            $custom_link_url = filter_var($custom_link_url, FILTER_SANITIZE_URL);

                            if ($custom_link_title == '' || $custom_link_url == '' || !filter_var($custom_link_url, FILTER_VALIDATE_URL)) {
                                continue;
                            }

                            $custom_link = [
                                'post_author' => get_current_user_id(),
                                'post_title' => $custom_link_title,
                                'post_status' => 'publish',
                                'post_type' => CustomLinks::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
                            ];

                            $custom_link_id = wp_insert_post($custom_link);

                            if (!is_wp_error($custom_link_id)) {
                                $id = $custom_link_id;
                                update_post_meta($id, CustomLinks::ILJ_FIELD_CUSTOM_LINKS_OPTION_NEW_WINDOW, 0);
                                update_post_meta($id, CustomLinks::ILJ_FIELD_CUSTOM_LINKS_OPTION_NOFOLLOW, 0);
                                update_post_meta($id, CustomLinks::ILJ_FIELD_CUSTOM_LINKS_URL, $custom_link_url);
                            }
                        }

                        if (!get_post($id)) {
                            continue;
                        }

                        update_post_meta($id, Postmeta::ILJ_META_KEY_LINKDEFINITION, $existing_keywords->getKeywords());
                    } elseif ($type == 'term') {
                        if (!$id || !get_term($id)) {
                            continue;
                        }

                        update_term_meta($id, Postmeta::ILJ_META_KEY_LINKDEFINITION, $existing_keywords->getKeywords());
                    }
                }
            }

            $import_count++;
        }

        fclose($handle);

        return $import_count;
    }
}
