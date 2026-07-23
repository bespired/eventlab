<?php

namespace EventLab\System\Controllers;

use EventLab\Core\Services\HandleFactory;
use EventLab\Core\Services\SecretsLoader;
use EventLab\Core\Support\Base62Converter;
use EventLab\Database\Managers\DatabaseConnectionManager;
use EventLab\System\Repositories\InstallRepository;
use Exception;
use PDO;

class InstallController
{
    public function handle($args): string
    {
        try {
            $action = $args->action ?? null;
            if (!$action) {
                return json_encode([
                    'status'  => 'error',
                    'message' => 'Action parameter missing.',
                ], JSON_PRETTY_PRINT);
            }

            $secrets = SecretsLoader::load();
            $secret  = $args->secret ?? '';
            if ($secret !== ($secrets['DB_SECRET'] ?? '')) {
                if (function_exists('http_response_code')) {
                    http_response_code(403);
                }

                return json_encode([
                    'status'  => 'error',
                    'message' => 'Unauthorized. Invalid secret key.',
                ], JSON_PRETTY_PRINT);
            }

            $dbManager = new DatabaseConnectionManager($secrets);
            $tenant    = $args->tenant ?? 'a0';

            // Ensure the global database exists before attempting to connect to it
            $dbManager->provisionGlobalDatabase();

            $globalPdo = $dbManager->getGlobalConnection();

            // Extract projects (array or single object into an array)
            $projectsData = [];
            if (isset($args->projects) && is_array($args->projects)) {
                $projectsData = $args->projects;
            } elseif (isset($args->project)) {
                $projectsData = is_array($args->project) && isset($args->project[0]) ? $args->project : [$args->project];
            }

            // Extract admins
            $adminsData = $args->admins ?? [];

            $results = [];

            switch ($action) {
                case 'install':
                case 'migrate':
                    // 1. Migrate Global
                    $converter     = new Base62Converter();
                    $handleFactory = new HandleFactory($converter);
                    $repository    = new InstallRepository($globalPdo, null, $handleFactory);
                    $dropAll       = ($action === 'install' || ($args->dropall ?? false));

                    $res = $repository->migrate($tenant, $dropAll, false);

                    $results['migration_global'] = $res['global'];

                    // 2. Process Projects
                    if ($action === 'install' && !empty($projectsData)) {
                        foreach ($projectsData as $project) {
                            $projectTenant         = $project->tenant ?? $tenant;
                            $res                   = $this->installProject($projectTenant, $project, $dbManager, $globalPdo, $adminsData);
                            $results['projects'][] = $res;
                        }
                    } elseif ($action === 'migrate') {
                        // If no projects explicitly passed, load active projects from `projects` table
                        if (empty($projectsData)) {
                            try {
                                $stmt         = $globalPdo->query('SELECT tenant, clientname, projectname, databasename FROM projects WHERE active = 1');
                                $projectsData = $stmt->fetchAll(PDO::FETCH_OBJ);
                            } catch (\PDOException $e) {
                                // Table might not exist yet
                            }
                        }

                        if (!empty($projectsData)) {
                            foreach ($projectsData as $project) {
                                $projectArray  = (array) $project;
                                $projectTenant = $projectArray['tenant'] ?? $tenant;
                                $dbName        = $projectArray['databasename'] ?? '';

                                if (!$dbName) {
                                    $clientname  = $projectArray['clientname'] ?? '';
                                    $projectname = $projectArray['projectname'] ?? '';
                                    if ($clientname && $projectname) {
                                        $dbName  = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $clientname . '_' . $projectname));
                                    }
                                }

                                // If dbName is still not resolved, query projects table by tenant
                                if (!$dbName && $projectTenant) {
                                    try {
                                        $stmt = $globalPdo->prepare('SELECT databasename, clientname, projectname FROM projects WHERE tenant = :tenant LIMIT 1');
                                        $stmt->execute([':tenant' => $projectTenant]);
                                        $pRow = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($pRow) {
                                            $dbName = $pRow['databasename'] ?: strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', ($pRow['clientname'] ?? '') . '_' . ($pRow['projectname'] ?? '')));
                                        }
                                    } catch (\PDOException $e) {
                                        // ignore
                                    }
                                }

                                if ($dbName) {
                                    try {
                                        $dbManager->provisionTenantDatabase($dbName);
                                        $tenantPdo = $dbManager->getTenantConnection($dbName);
                                        $repo      = new InstallRepository($globalPdo, $tenantPdo, $handleFactory);
                                        $res       = $repo->migrate($projectTenant, false, false);

                                        $results['migration_tenant'][$dbName] = $res['tenant'];
                                    } catch (\PDOException $e) {
                                        // ignore if connection fails during migrate
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'project':
                    if (empty($projectsData)) {
                        throw new Exception("Project configuration missing for action 'project'.");
                    }
                    foreach ($projectsData as $project) {
                        $projectTenant = $project->tenant ?? $tenant;
                        $res           = $this->installProject($projectTenant, $project, $dbManager, $globalPdo, $adminsData);

                        $results['projects'][] = $res;
                    }
                    break;

                case 'admins':
                    if (empty($adminsData)) {
                        throw new Exception("Admins list missing for action 'admins'.");
                    }
                    if (empty($projectsData)) {
                        throw new Exception("Project configuration is required to resolve tenant database for action 'admins'.");
                    }
                    foreach ($projectsData as $project) {
                        $projectTenant              = $project->tenant ?? $tenant;
                        $clientname                 = $project->clientname ?? '';
                        $projectname                = $project->projectname ?? '';
                        $dbName                     = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $clientname . '_' . $projectname));
                        $tenantPdo                  = $dbManager->getTenantConnection($dbName);
                        $res                        = $this->installAdmins($projectTenant, $adminsData, $globalPdo, $tenantPdo);
                        $results['admins'][$dbName] = $res;
                    }
                    break;

                default:
                    if (function_exists('http_response_code')) {
                        http_response_code(400);
                    }

                    return json_encode([
                        'status'  => 'error',
                        'message' => "Invalid action: '$action'",
                    ], JSON_PRETTY_PRINT);
            }

            return json_encode([
                'status'  => 'success',
                'message' => 'Action executed successfully.',
                'details' => $results,
                'time'    => date('Y-m-d H:i:s'),
            ], JSON_PRETTY_PRINT);
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

    public function installProject(
        string $tenant, $projectData,
        DatabaseConnectionManager $dbManager,
        PDO $globalPdo,
        array $globalAdminsData = []): array
    {
        $projectDataArray = (array) $projectData;
        $clientname       = $projectDataArray['clientname'] ?? '';
        $projectname      = $projectDataArray['projectname'] ?? '';

        if (empty($clientname) || empty($projectname)) {
            throw new Exception('clientname and projectname are required.');
        }

        $dbName = $projectDataArray['databasename'] ?? strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $clientname . '_' . $projectname));

        // Provision the database
        $dbManager->provisionTenantDatabase($dbName);
        $tenantPdo = $dbManager->getTenantConnection($dbName);

        $converter     = new Base62Converter();
        $handleFactory = new HandleFactory($converter);
        $repository    = new InstallRepository($globalPdo, $tenantPdo, $handleFactory);

        // Migrate tenant
        $repoRes = $repository->migrate($tenant, true, true);

        // Record in project global table
        $projectHandle = $repository->createProject($tenant, $projectDataArray);

        // Extract admins from project data or fallback to global ones
        $projectAdmins = $projectDataArray['admins'] ?? $globalAdminsData;

        // Install admins for this project
        $adminsInstalled = [];
        if (!empty($projectAdmins)) {
            $adminRes        = $this->installAdmins($tenant, $projectAdmins, $globalPdo, $tenantPdo);
            $adminsInstalled = $adminRes['installed'];
        }

        return [
            'database'   => $dbName,
            'handle'     => $projectHandle,
            'status'     => 'created',
            'migrations' => $repoRes['tenant'],
            'admins'     => $adminsInstalled,
        ];
    }

    public function installAdmins(string $tenant, array $adminsData, PDO $globalPdo, PDO $tenantPdo): array
    {
        $converter     = new Base62Converter();
        $handleFactory = new HandleFactory($converter);
        $repository    = new InstallRepository($globalPdo, $tenantPdo, $handleFactory);

        $installed = [];
        foreach ($adminsData as $admin) {
            $adminArray = (array) $admin;
            $email      = $adminArray['email'] ?? null;
            if ($email) {
                $repository->createAdmin($tenant, $adminArray);
                $installed[] = $email;
            }
        }

        return [
            'installed' => $installed,
            'status'    => 'success',
        ];
    }
}
