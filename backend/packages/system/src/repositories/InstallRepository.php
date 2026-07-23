<?php

namespace EventLab\System\Repositories;

use EventLab\Core\Services\HandleFactory;
use PDO;

class InstallRepository
{
    private $globalPdo;
    private $tenantPdo;
    private $handleFactory;

    public function __construct(PDO $globalPdo, ?PDO $tenantPdo, HandleFactory $handleFactory)
    {
        $this->globalPdo     = $globalPdo;
        $this->tenantPdo     = $tenantPdo;
        $this->handleFactory = $handleFactory;
    }

    public function migrate(string $tenant, bool $dropAll, bool $shouldProvision = false): array
    {
        $results = ['global' => ['migrations' => 0, 'seeds' => 0], 'tenant' => ['migrations' => 0, 'seeds' => 0]];

        $orderFile = __DIR__ . '/../../../../database/order.php';
        $dbBase    = __DIR__ . '/../../../../database';

        $allMigrations = [];
        if (file_exists($orderFile)) {
            require $orderFile; // Defines $allMigrations
        }

        // --- Global: tables + seeds (only when migrating global DB) ---
        if ($this->tenantPdo === null) {
            $globalDbName = $this->getDbName($this->globalPdo);

            $globalTableFiles = [];
            $globalSeedFiles  = [];
            if (isset($allMigrations['globals'])) {
                foreach ($allMigrations['globals'] as $path => $type) {
                    $abs = realpath($dbBase . $path);
                    if ($type === 'table') {
                        $globalTableFiles[$path] = $abs;
                    } elseif ($type === 'seed') {
                        $globalSeedFiles[$path] = $abs;
                    }
                }
            } else {
                foreach (glob($dbBase . '/globals/*.sql') as $abs) {
                    $rel                    = '/globals/' . basename($abs);
                    $globalTableFiles[$rel] = $abs;
                }
            }

            // Fetch already-run entries for the global database
            $alreadyRunGlobal = $this->fetchAlreadyRun($globalDbName);

            foreach ($globalTableFiles as $rel => $file) {
                if ($file && file_exists($file)) {
                    $name = ltrim($rel, '/');
                    if (!$dropAll && isset($alreadyRunGlobal[$name])) {
                        continue;
                    }
                    $sql = file_get_contents($file);
                    if ($sql) {
                        try {
                            $this->globalPdo->exec($sql);
                            $this->recordMigration('migration', $globalDbName, $name);
                            $results['global']['migrations']++;
                        } catch (\PDOException $e) {
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                throw $e;
                            }
                        }
                    }
                }
            }

            foreach ($globalSeedFiles as $rel => $file) {
                if ($file && file_exists($file)) {
                    $name = ltrim($rel, '/');
                    if (!$dropAll && isset($alreadyRunGlobal[$name])) {
                        continue;
                    }
                    $sql = file_get_contents($file);
                    if ($sql) {
                        try {
                            $this->globalPdo->exec($sql);
                            $this->recordMigration('seed', $globalDbName, $name);
                            $results['global']['seeds']++;
                        } catch (\PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate entry') === false
                                && strpos($e->getMessage(), 'already exists') === false) {
                                throw $e;
                            }
                        }
                    }
                }
            }
        }

        // --- Tenant: tables + seeds (when migrating dynamic tenant DB) ---
        if ($this->tenantPdo) {
            $tenantDbName = $this->getDbName($this->tenantPdo);

            $tbltypes = ['dynamics', 'builders'];

            foreach ($tbltypes as $tbltype) {
                $tenantTableFiles = [];
                $tenantSeedFiles  = [];
                if (isset($allMigrations[$tbltype])) {
                    foreach ($allMigrations[$tbltype] as $path => $type) {
                        $abs = realpath($dbBase . $path);
                        if ($type === 'table') {
                            $tenantTableFiles[$path] = $abs;
                        } elseif ($type === 'seed') {
                            $tenantSeedFiles[$path] = $abs;
                        }
                    }
                } else {
                    foreach (glob($dbBase . "/$tbltype/*.sql") as $abs) {
                        $rel                    = "/$tbltype/" . basename($abs);
                        $tenantTableFiles[$rel] = $abs;
                    }
                }

                $alreadyRunTenant = $this->fetchAlreadyRun($tenantDbName);

                foreach ($tenantTableFiles as $rel => $file) {
                    if ($file && file_exists($file)) {
                        $name = ltrim($rel, '/');
                        if (!$dropAll && isset($alreadyRunTenant[$name])) {
                            continue;
                        }
                        $sql = file_get_contents($file);
                        if ($sql) {
                            $sql = $this->installUnroll($sql);

                            try {
                                $this->tenantPdo->exec($sql);
                                $this->recordMigration('migration', $tenantDbName, $name);
                                $results['tenant']['migrations']++;
                            } catch (\PDOException $e) {
                                if (strpos($e->getMessage(), 'already exists') === false) {
                                    throw $e;
                                }
                            }
                        }
                    }
                }

                foreach ($tenantSeedFiles as $rel => $file) {
                    if ($file && file_exists($file)) {
                        $name = ltrim($rel, '/');
                        if (!$dropAll && isset($alreadyRunTenant[$name])) {
                            continue;
                        }
                        $sql = file_get_contents($file);
                        if ($sql) {
                            try {
                                $this->tenantPdo->exec($sql);
                                $this->recordMigration('seed', $tenantDbName, $name);
                                $results['tenant']['seeds']++;
                            } catch (\PDOException $e) {
                                if (strpos($e->getMessage(), 'Duplicate entry') === false
                                && strpos($e->getMessage(), 'already exists') === false) {
                                    throw $e;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $results;
    }

    private function installUnroll($sqltpl)
    {
        // Match "-- loop N", followed by the body, up to the closing "--"
        $pattern = '/--\s*loop\s+(\d+)\s*\r?\n([\s\S]*?)\r?\n--/';

        return preg_replace_callback($pattern, function ($matches) {
            $count    = (int) $matches[1];
            $template = $matches[2];
            $unrolled = [];

            for ($i = 1; $i <= $count; $i++) {
                // Replace the '@' symbol with the current iteration index
                $unrolled[] = str_replace('@', $i, $template);
            }

            // Join the unrolled lines.
            // You can use "\n" or "\n\n" depending on how much spacing you want.
            return implode("\n", $unrolled);
        }, $sqltpl);
    }

    private function getDbName(PDO $pdo): string
    {
        try {
            return (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
        } catch (\PDOException $e) {
            return '';
        }
    }

    /**
     * Fetch all already-recorded migration/seed names for a target database name.
     * Returns an associative array keyed by name for O(1) lookup.
     */
    private function fetchAlreadyRun(string $dbName): array
    {
        if (!$dbName) {
            return [];
        }

        try {
            $stmt = $this->globalPdo->prepare('SELECT `name` FROM `migrationseeds` WHERE `db` = :db');
            $stmt->execute([':db' => $dbName]);
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return array_flip($rows);
        } catch (\PDOException $e) {
            // Table not yet created – that's fine on first install
            return [];
        }
    }

    /**
     * Record a successfully-run file in the migrationseeds tracking table for the target database.
     * Silently ignores failures (e.g. if migrationseeds itself was just created this run).
     */
    private function recordMigration(string $type, string $dbName, string $name): void
    {
        try {
            $stmt = $this->globalPdo->prepare(
                'INSERT IGNORE INTO `migrationseeds` (`type`, `db`, `name`) VALUES (:type, :db, :name)'
            );
            $stmt->execute([':type' => $type, ':db' => $dbName, ':name' => $name]);
        } catch (\PDOException $e) {
            // Silently ignore – migrationseeds may not exist on the very first run
        }
    }

    public function createProject(string $tenant, array $projectData): string
    {
        $handle        = $this->handleFactory->create('projects', $tenant);
        // Use NULL (not empty string) when no slug is set — UNIQUE KEY allows multiple NULLs
        $fallback_slug = isset($projectData['fallback_slug']) && $projectData['fallback_slug'] !== ''
            ? $projectData['fallback_slug']
            : null;
        $databasename  = $projectData['databasename'] ?? strtolower(
            preg_replace('/[^a-zA-Z0-9_]/', '', ($projectData['clientname'] ?? '') . '_' . $projectData['projectname'])
        );

        $stmt = $this->globalPdo->prepare('
            INSERT IGNORE INTO projects (handle, tenant, fallback_slug, projectname, clientname, custom_domain, databasename)
            VALUES (:handle, :tenant, :fallback_slug, :projectname, :clientname, :custom_domain, :databasename)
        ');
        $stmt->execute([
            ':handle'        => $handle,
            ':tenant'        => $tenant,
            ':fallback_slug' => $fallback_slug,
            ':projectname'   => $projectData['projectname'],
            ':clientname'    => $projectData['clientname'] ?? '',
            ':custom_domain' => $projectData['custom_domain'] ?? null,
            ':databasename'  => $databasename,
        ]);

        return $handle;
    }

    public function createAdmin(string $tenant, array $adminData): void
    {
        $email = $adminData['email'];

        // 1. Insert into global user_logins
        $userHandle = $this->handleFactory->create('user_logins', $tenant);
        $stmtUser   = $this->globalPdo->prepare('
            INSERT IGNORE INTO user_logins (handle, email, hash)
            VALUES (:handle, :email, :hash)
        ');
        $stmtUser->execute([
            ':handle' => $userHandle,
            ':email'  => $email,
            ':hash'   => password_hash($adminData['password'] ?? 'admin', PASSWORD_DEFAULT),
        ]);

        $stmtSelect = $this->globalPdo->prepare('SELECT handle FROM user_logins WHERE email = :email');
        $stmtSelect->execute([':email' => $email]);
        $row = $stmtSelect->fetch();
        if (!$row) {
            return;
        }
        $realUserHandle = $row['handle'];

        if ($this->tenantPdo) {
            // 2. Insert into tenant prospects
            $prospectHandle = $this->handleFactory->create('prospects', $tenant);
            $stmtProspect   = $this->tenantPdo->prepare('
                INSERT IGNORE INTO prospects (handle, email, is_backend)
                VALUES (:handle, :email, 1)
            ');
            $stmtProspect->execute([
                ':handle' => $prospectHandle,
                ':email'  => $email,
            ]);

            $stmtPSelect = $this->tenantPdo->prepare('SELECT handle FROM prospects WHERE email = :email');
            $stmtPSelect->execute([':email' => $email]);
            $prow = $stmtPSelect->fetch();
            if ($prow) {
                $realProspectHandle = $prow['handle'];

                // 3. Insert into global user_tenants
                $stmtTenant = $this->globalPdo->prepare('
                    INSERT IGNORE INTO user_tenants (user, tenant, prospect)
                    VALUES (:user, :tenant, :prospect)
                ');
                $stmtTenant->execute([
                    ':user'     => $realUserHandle,
                    ':tenant'   => $tenant,
                    ':prospect' => $realProspectHandle,
                ]);

                // 4. Insert into global user_roles
                $stmtRole = $this->globalPdo->prepare('
                    INSERT IGNORE INTO user_roles (prospect, tenant, role)
                    VALUES (:prospect, :tenant, :role)
                ');
                $stmtRole->execute([
                    ':prospect' => $realProspectHandle,
                    ':tenant'   => $tenant,
                    ':role'     => 'admin',
                ]);
            }
        }
    }
}
