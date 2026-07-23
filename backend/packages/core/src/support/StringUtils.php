<?php
namespace EventLab\Core\Support;

/**
 * General-purpose string utilities.
 * Direct conversion of the Utils trait to a plain static class.
 */
class StringUtils
{
    /**
     * Converts any string into a URL-safe lowercase slug.
     */
    public static function slug(string $text, string $divider = '-'): string
    {
        // Replace non-letter/digit characters with the divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // Transliterate to ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Remove remaining unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim and de-duplicate dividers
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);

        // Lowercase
        $text = strtolower($text);

        return empty($text) ? 'n-a' : $text;
    }
}
