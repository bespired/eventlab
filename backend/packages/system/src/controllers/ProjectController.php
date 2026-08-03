<?php

namespace EventLab\System\Controllers;

use EventLab\Core\Services\SecretsLoader;
use EventLab\Database\Managers\DatabaseConnectionManager;
use PDO;

class ProjectController
{
    public function handle($args)
    {
        $action = $args->action ?? 'list';

        switch ($action) {
            case 'list':
                return $this->listProjects($args);

            default:
                http_response_code(400);

                return ['status' => 'error', 'message' => 'Invalid action'];
        }
    }

    private function listProjects($args)
    {
        try {
            $secrets = SecretsLoader::load();
            $dbManager = new DatabaseConnectionManager($secrets);
            $pdo = $dbManager->getGlobalConnection();

            $stmt = $pdo->query("SELECT handle, tenant, clientname, projectname, active FROM projects WHERE active = 1");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($projects)) {
                $projects = [
                    ['handle' => 'a0', 'tenant' => 'a0', 'clientname' => 'Eventlab', 'projectname' => 'Eventlab', 'active' => 1],
                    ['handle' => 'c3', 'tenant' => 'c3', 'clientname' => 'Project2', 'projectname' => 'Project2', 'active' => 1],
                ];
            }

            return [
                'status'   => 'success',
                'projects' => $projects,
            ];
        } catch (\Exception $e) {
            return [
                'status'   => 'success',
                'projects' => [
                    ['handle' => 'a0', 'tenant' => 'a0', 'clientname' => 'Eventlab', 'projectname' => 'Eventlab', 'active' => 1],
                    ['handle' => 'c3', 'tenant' => 'c3', 'clientname' => 'Project2', 'projectname' => 'Project2', 'active' => 1],
                ],
                'note'     => $e->getMessage(),
            ];
        }
    }
}
