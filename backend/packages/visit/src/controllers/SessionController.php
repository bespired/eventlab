<?php

namespace EventLab\Visit\Controllers;

use EventLab\Core\Services\SecretsLoader;
use EventLab\Database\Helpers\QueryTracker;
use EventLab\Database\Managers\DatabaseConnectionManager;
use EventLab\Visit\Services\IncomingVisitHandler;
use Exception;

class SessionController
{
    public function handle($args): string
    {
        try {
            $action = $args->action ?? null;

            switch ($action) {
                case 'visit':
                    return $this->handleIncoming($args);

                default:
                    if (function_exists('http_response_code')) {
                        http_response_code(400);
                    }

                    return json_encode([
                        'status'  => 'error',
                        'message' => "Invalid action: '{$action}'",
                    ], JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            if (function_exists('http_response_code')) {
                http_response_code(500);
            }

            return json_encode([
                'status'        => 'error',
                'message'       => 'Internal Server Error',
                'failed_query'  => QueryTracker::$lastSql,
                'query_params'  => QueryTracker::$lastParams,
                'error_details' => $e->getMessage(),
            ], JSON_PRETTY_PRINT);
        }
    }

    private function handleIncoming($args): string
    {
        $init = $args->init ?? null;
        if (!$init) {
            return json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
        }

        $secrets   = SecretsLoader::load();
        $dbManager = new DatabaseConnectionManager($secrets);
        $globalPdo = $dbManager->getGlobalConnection();

        $json = base64_decode($init);
        $init = json_decode($json);

        $handler  = new IncomingVisitHandler($globalPdo);
        $response = $handler->handleIncoming($args);

        return json_encode([
            'args'    => $init,
            'status'  => 'success',
            'message' => 'handling the visit.',
            'handle'  => $response,
        ], JSON_PRETTY_PRINT);
    }

    // private function handleIncoming($args): string
    // {
    //     $stmt = $this->globalPdo->prepare('SELECT id FROM lut_agents WHERE agent_string = :agent');
    //     $stmt->execute(['agent' => $userAgent]);
    //     $id = $stmt->fetchColumn();
    // }
}
