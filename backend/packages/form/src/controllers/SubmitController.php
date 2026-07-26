<?php
namespace EventLab\Form\Controllers;

use EventLab\Core\Services\HandleFactory;
use EventLab\Core\Services\SecretsLoader;
use EventLab\Core\Support\Base62Converter;
use EventLab\Database\Managers\DatabaseConnectionManager;
use EventLab\Form\Repositories\SubmitRepository;
use EventLab\Prospect\Repositories\ContactRepository;
use Exception;

class SubmitController
{
    public function handle($args): string
    {
        try {
            $action = $args->action ?? null;
            switch ($action) {
                case 'incomming':
                    return $this->handleIncoming($args);

                case 'process':
                    return $this->handleProcess($args);

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
        if ($error = $this->whatErrors($args)) {
            return json_encode([
                'status'  => 'error',
                'message' => "Error, $error",
            ]);
        }

        $tenant     = $args->tenant;
        $repository = $this->resolveRepository($tenant);

        // Store the args in puls_forms table in tenant DB
        $handle = $repository->saveFormSubmission($tenant, $args);

        // Kick the handler process asynchronously
        $runPhpPath = realpath(__DIR__ . '/../../../../run.php');
        if ($runPhpPath && file_exists($runPhpPath)) {
            $tpl = 'php %s formsubmit %s > /dev/null 2>&1 &';
            $cmd = sprintf($tpl, escapeshellarg($runPhpPath), escapeshellarg($handle));
            exec($cmd);
        }

        return json_encode([
            'status'  => 'success',
            'message' => 'handling the form.',
            'handle'  => $handle,
        ], JSON_PRETTY_PRINT);
    }

    private function handleProcess($args): string
    {
        $handle = $args->handle ?? null;
        if (! $handle) {
            return json_encode([
                'status'  => 'error',
                'message' => "Missing 'handle' parameter for form processing.",
            ], JSON_PRETTY_PRINT);
        }

        $tenant     = $args->tenant ?? substr($handle, 0, 2);
        $repository = $this->resolveRepository($tenant);

        $result = $repository->processFormSubmission($tenant, $handle);

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    private function resolveRepository(string $tenant): SubmitRepository
    {
        $secrets   = SecretsLoader::load();
        $dbManager = new DatabaseConnectionManager($secrets);
        $globalPdo = $dbManager->getGlobalConnection();

        $stmt = $globalPdo->prepare('SELECT databasename FROM projects WHERE tenant = :tenant LIMIT 1');
        $stmt->execute([':tenant' => $tenant]);
        $project = $stmt->fetch();

        if (! $project || empty($project['databasename'])) {
            throw new Exception("No project found for tenant '{$tenant}'.");
        }

        $dbName    = $project['databasename'];
        $tenantPdo = $dbManager->getTenantConnection($dbName);

        $converter         = new Base62Converter();
        $handleFactory     = new HandleFactory($converter);
        $contactRepository = new ContactRepository($globalPdo, $tenantPdo, $handleFactory);

        return new SubmitRepository($globalPdo, $tenantPdo, $handleFactory, $contactRepository);
    }

    private function whatErrors($args)
    {
        if (! isset($args->tenant) || empty($args->tenant)) {
            return "tenant missing.";
        }

        if (! isset($args->form)) {
            return "form data missing.";
        }

        if (! isset($args->sys) || empty($args->sys)) {
            return "form validation missing.";
        }

        return null;
    }
}
