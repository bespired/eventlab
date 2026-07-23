<?php

namespace EventLab\Prospect\Controllers;

use EventLab\Core\Services\HandleFactory;
use EventLab\Core\Services\SecretsLoader;
use EventLab\Core\Support\Base62Converter;
use EventLab\Database\Managers\DatabaseConnectionManager;
use EventLab\Prospect\Repositories\ContactRepository;
use Exception;

class ContactController
{
    public function handle($args): string
    {
        try {
            $action = $args->action ?? null;

            $secrets   = SecretsLoader::load();
            $dbManager = new DatabaseConnectionManager($secrets);
            $globalPdo = $dbManager->getGlobalConnection();

            switch ($action) {
                case 'import':
                    return $this->importContacts($args, $globalPdo, $dbManager, $secrets);

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

    // -------------------------------------------------------------------------

    private function importContacts($args, $globalPdo, DatabaseConnectionManager $dbManager, array $secrets): string
    {
        $tenant   = $args->tenant ?? null;
        $contacts = $args->prospects->contacts ?? [];

        if (! $tenant) {
            throw new Exception("'tenant' is required for contact import.");
        }

        if (empty($contacts)) {
            return json_encode([
                'status'  => 'success',
                'message' => 'No contacts provided.',
                'imported' => 0,
            ], JSON_PRETTY_PRINT);
        }

        // Resolve tenant database name from the global projects table
        $stmt = $globalPdo->prepare('SELECT databasename FROM projects WHERE tenant = :tenant LIMIT 1');
        $stmt->execute([':tenant' => $tenant]);
        $project = $stmt->fetch();

        if (! $project || empty($project['databasename'])) {
            throw new Exception("No project found for tenant '{$tenant}'. Run install first.");
        }

        $dbName    = $project['databasename'];
        $tenantPdo = $dbManager->getTenantConnection($dbName);

        $converter     = new Base62Converter();
        $handleFactory = new HandleFactory($converter);
        $repository    = new ContactRepository($globalPdo, $tenantPdo, $handleFactory);

        // Convert stdClass contact objects to plain arrays
        $contactArrays = array_map(fn ($c) => (array) $c, (array) $contacts);

        $result = $repository->importContacts($tenant, $contactArrays);

        return json_encode([
            'status'   => 'success',
            'tenant'   => $tenant,
            'database' => $dbName,
            'imported' => $result['imported'],
            'errors'   => $result['errors'],
            'time'     => date('Y-m-d H:i:s'),
        ], JSON_PRETTY_PRINT);
    }
}
