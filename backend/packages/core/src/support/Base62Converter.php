<?php
namespace EventLab\Core\Support;

/**
 * Encodes integers to Base62 for compact, URL-safe handle timestamps.
 * Direct conversion of the Base62Convert trait to a plain injectable class.
 */
class Base62Converter
{
    private const CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public function encode(int $timestamp): string
    {
        if ($timestamp === 0) {
            return self::CHARS[0];
        }

        $base62 = '';
        while ($timestamp > 0) {
            $remainder = $timestamp % 62;
            $base62    = self::CHARS[$remainder] . $base62;
            $timestamp = intdiv($timestamp, 62);
        }

        // Keep the last 6 characters — enough for Unix timestamps through 2059
        return substr($base62, -6);
    }
}
