<?php

namespace EventLab\Database\Services;

use EventLab\Core\Services\HandleFactory;
use EventLab\Database\Helpers\QueryTracker;
use PDO;

class LutHandler
{
    private PDO $globalPdo;
    private HandleFactory $handleFactory;

    public function __construct(PDO $globalPdo, HandleFactory $handleFactory)
    {
        $this->globalPdo     = $globalPdo;
        $this->handleFactory = $handleFactory;
    }

    public function handle(string $table, string $value): string
    {
        $sql    = "SELECT `handle` FROM `$table` WHERE `match` = :value";
        $params = ['value' => $value];

        QueryTracker::set($sql, $params);

        $stmt = $this->globalPdo->prepare($sql);
        $stmt->execute($params);
        $handle = $stmt->fetchColumn();

        if ($handle) {
            return $handle;
        }

        $newHandle = $this->handleFactory->create($table);

        $sql    = "INSERT INTO `$table` (`handle`, `match`) VALUES (:handle, :value)";
        $params = ['handle' => $newHandle, 'value'  => $value];

        QueryTracker::set($sql, $params);

        $insert = $this->globalPdo->prepare($sql);
        $insert->execute($params);

        QueryTracker::reset();

        return $newHandle;
    }
}
