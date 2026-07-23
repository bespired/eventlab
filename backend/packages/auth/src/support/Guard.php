<?php

namespace EventLab\Auth\Support;

use EventLab\Auth\Repositories\UserRoleRepository;
use EventLab\Auth\Services\AuthorizationService;
use PDO;

class Guard
{
    private static ?PDO $pdo = null;

    /**
     * Lazy-loads the PDO connection if it hasn't been created yet.
     */
    private static function getPdo(): PDO
    {
        if (self::$pdo !== null) {
            try {
                self::$pdo->query('SELECT 1');  // Quick check if connection is alive
            } catch (\PDOException $e) {
                self::$pdo = null;              // Connection died; reset it
            }
        }

        if (self::$pdo === null) {
            // Load secrets / database config (adjust path to match your structure)
            $envFile = __DIR__ . '/../../../../config/secrets/development.php';
            $env     = file_exists($envFile) ? require $envFile : [];

            $host    = $env['DB_HOST'] ?? 'db';
            $db      = $env['DB_NAME'] ?? 'eventlab';
            $user    = $env['DB_USER'] ?? 'eventlab_user';
            $pass    = $env['DB_PASS'] ?? 'eventlab_password';
            $charset = 'utf8mb4';

            $dsn     = "mysql:host={$host};dbname={$db};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // self::$pdo = new PDO($dsn, $user, $pass, $options);
            $attempts = 0;
            while (true) {
                try {
                    self::$pdo = new PDO($dsn, $user, $pass, $options);
                    break;
                } catch (\PDOException $e) {
                    $attempts++;
                    if ($attempts >= 3) {
                        throw $e;
                    }
                    usleep(100000); // Wait 100ms before retrying
                }
            }
        }

        return self::$pdo;
    }

    /**
     * Optional: Allows passing an existing PDO (e.g. from run.php) to avoid duplicate connections.
     */
    public static function setPdo(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * Main guard check — called anywhere in your app.
     */
    public static function check($args, array $roles = []): bool
    {
        $pdo = self::getPdo();

        $repo    = new UserRoleRepository($pdo);
        $service = new AuthorizationService($repo);

        $token  = $args->token ?? null;
        $tenant = $args->tenant ?? null;

        return $service->isGranted($token, $roles, $tenant);
    }
}
