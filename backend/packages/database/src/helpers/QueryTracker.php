<?php

namespace EventLab\Database\Helpers;

class QueryTracker
{
    public static string $lastSql   = '';
    public static array $lastParams = [];

    public static function set(string $sql, array $params = []): void
    {
        self::$lastSql    = $sql;
        self::$lastParams = $params;
    }

    public static function reset(): void
    {
        self::$lastSql    = 'No query set in QueryTracker';
        self::$lastParams = [];
    }
}
