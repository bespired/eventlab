<?php

namespace EventLab\Visit\Controllers;

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

        $json = base64_decode($init);
        $init = json_decode($json);

        // 1. Create shared base objects
        $factory   = new EventLab\Core\Services\HandleFactory();

        // 2. Create services (passing PDO and Factory)
        $lutAgent  = new EventLab\Database\Services\LutAgent($globalPdo, $factory);
        $lutDevice = new EventLab\Database\Services\LutDevice($globalPdo, $factory);

        // 3. Create your handler
        $handler   = new EventLab\Controllers\IncomingVisitHandler($lutAgent, $lutDevice);

        // 4. Run your method!
        $response  = $handler->handleIncoming($args);

        // $handler = new IncomingVisitHandler();
        // $handler->handleIncoming($args);

        // $agent = $_SERVER['HTTP_USER_AGENT'];

        return json_encode([
            'browser' => $browser,
            'args'    => $init,
            'status'  => 'success',
            'message' => 'handling the visit.',
            'handle'  => 'a0pp-12345678-12345678',
        ], JSON_PRETTY_PRINT);
    }

    // private function handleIncoming($args): string
    // {
    //     $stmt = $this->globalPdo->prepare('SELECT id FROM lut_agents WHERE agent_string = :agent');
    //     $stmt->execute(['agent' => $userAgent]);
    //     $id = $stmt->fetchColumn();
    // }
}
