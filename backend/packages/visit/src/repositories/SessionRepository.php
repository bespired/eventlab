<?php

namespace EventLab\Visit\Repositories;

use EventLab\Core\Services\HandleFactory;
use EventLab\Prospect\Repositories\ContactRepository;
use PDO;

class SessionRepository
{
    private PDO $globalPdo;
    private PDO $tenantPdo;
    private HandleFactory $handleFactory;
    private ContactRepository $contactRepository;

    public function __construct(
        PDO $globalPdo,
        PDO $tenantPdo,
        HandleFactory $handleFactory,
        ContactRepository $contactRepository
    ) {
        $this->globalPdo         = $globalPdo;
        $this->tenantPdo         = $tenantPdo;
        $this->handleFactory     = $handleFactory;
        $this->contactRepository = $contactRepository;
    }

    // Grab your query helper directly
    // $db = $dbManager->getTenantQueryHelper($tenantDbName);

    // Clean, single-line insert
    // $db->insert('puls_forms', [
    //     'handle'   => $handle,
    //     'prospect' => $prospect,
    //     'name'     => $name,
    //     'formdata' => json_encode($formData),
    // ]);
}
