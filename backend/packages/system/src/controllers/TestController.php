<?php

namespace EventLab\System\Controllers;

use EventLab\Core\Services\HandleFactory;
use EventLab\Core\Services\SecretsLoader;
use EventLab\Core\Support\Base62Converter;

class TestController
{
    public function handle($args)
    {
        $action = $args->action ?? null;

        switch ($action) {
            case 'server':
                return $this->dockerTest($args);

            case 'secrets':
                return $this->secretsTest($args);

            case 'handle':
                return $this->handleTest($args);

            default:
                http_response_code(400);

                return ['status' => 'error', 'message' => 'Invalid action'];
        }
    }

    private function dockerTest($args)
    {
        return ['status' => 'success', 'message' => 'Server active.'];
    }

    private function secretsTest($args)
    {
        $secrets = SecretsLoader::load();

        return [
            'status'   => 'success',
            'message'  => 'Secrets found.',
            'database' => $secrets['DB_NAME'],
        ];
    }

    private function handleTest($args)
    {
        // 1. Manually instantiate the required chain inline
        $converter     = new Base62Converter();
        $handleFactory = new HandleFactory($converter);

        // 2. Fallback values so the test endpoint doesn't crash if params are missing
        $table  = $args->table ?? 'projects';
        $tenant = $args->tenant ?? 'a0';

        // 3. Generate the handle directly
        $mockHandle = $handleFactory->create($table, $tenant);

        return [
            'status'  => 'success',
            'handle'  => $mockHandle,
            'message' => 'Test handle generated.',
        ];
    }
}
