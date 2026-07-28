<?php

namespace EventLab\Database\Services;

use EventLab\Core\Services\HandleFactory;
use PDO;

class LutAgent
{
    private PDO $globalPdo;
    private HandleFactory $handleFactory;

    public function __construct(PDO $globalPdo, HandleFactory $handleFactory)
    {
        $this->globalPdo     = $globalPdo;
        $this->handleFactory = $handleFactory;
    }

    public function handle(string $userAgent): string
    {
        // 1. Check if user agent already exists
        $stmt = $this->globalPdo->prepare('SELECT handle FROM lut_agents WHERE match = :agent');
        $stmt->execute(['agent' => $userAgent]);
        $handle = $stmt->fetchColumn();

        if ($handle) {
            return $handle;
        }

        // 2. Generate a new handle for lut_agents
        $newHandle = $this->handleFactory->create('lut_agents');

        // 3. Insert new agent record
        $insert = $this->globalPdo->prepare('INSERT INTO lut_agents (handle, match) VALUES (:handle, :agent)');
        $insert->execute([
            'handle' => $newHandle,
            'agent'  => $userAgent,
        ]);

        return $newHandle;
    }
}
