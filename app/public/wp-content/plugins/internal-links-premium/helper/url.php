<?php
namespace ILJ\Helper;

/**
 * URL toolset
 *
 * Methods for URL manipulation / parsing and generating
 *
 * @package ILJ\Helper
 *
 * @since 1.2.2
 */
class Url
{
    /**
     * Converts a list of urls from relative to absolute paths
     *
     * @since 1.2.2
     * @param array $urls The URL array
     *
     * @return array
     */
    public static function convertRelativePathsToAbsolute(array $urls)
    {
        for($i = 0; $i < count($urls); $i++) {
            $parsed_url = parse_url($urls[$i]);

            if ((isset($parsed_url['scheme']) && isset($parsed_url['host'])) || !isset($parsed_url['path'])) {
                continue;
            }

            $urls[$i] = home_url($parsed_url['path']);
        }
        return $urls;
    }
}

