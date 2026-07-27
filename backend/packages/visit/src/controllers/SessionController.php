<?php

namespace EventLab\Visit\Controllers;

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

        return json_encode([
            'args'    => $init,
            'status'  => 'success',
            'message' => 'handling the visit.',
            'handle'  => 'a0pp-12345678-12345678',
        ], JSON_PRETTY_PRINT);
    }
}
