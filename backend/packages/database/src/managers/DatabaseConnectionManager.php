<?php

namespace EventLab\Database\Managers;

use PDO;

class DatabaseConnectionManager
{
    private $secrets;
    private $globalPdo;
    private $tenantPdos = [];

    public function __construct(array $secrets)
    {
        $this->secrets = $secrets;
    }

    /**
     * Resolves the correct host based on whether the script runs inside Docker.
     */
    private function resolveHost(): string
    {
        $host       = $this->secrets['DB_HOST'] ?? '127.0.0.1';
        $dockerHost = $this->secrets['DB_DOCKER'] ?? 'db';

        // If running inside a container, prefer the Docker bridge service name
        if (file_exists('/.dockerenv')) {
            return $dockerHost;
        }

        return $host;
    }

    public function getGlobalConnection(): PDO
    {
        if ($this->globalPdo) {
            return $this->globalPdo;
        }

        $host   = $this->resolveHost();
        $user   = $this->secrets['DB_USER'] ?? 'root';
        $pass   = $this->secrets['DB_PASS'] ?? '';
        $dbname = $this->secrets['DB_NAME'] ?? 'eventlab_global';
        $port   = $this->secrets['DB_PORT'] ?? 3306;

        $dsn             = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        $this->globalPdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $this->globalPdo;
    }

    public function getTenantConnection(string $dbName): PDO
    {
        if (isset($this->tenantPdos[$dbName])) {
            return $this->tenantPdos[$dbName];
        }

        $host = $this->resolveHost();
        $user = $this->secrets['DB_USER'] ?? 'root';
        $pass = $this->secrets['DB_PASS'] ?? '';
        $port = $this->secrets['DB_PORT'] ?? 3306;

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->tenantPdos[$dbName] = $pdo;

        return $pdo;
    }

    public function provisionGlobalDatabase(): void
    {
        $dbname = $this->secrets['DB_NAME'] ?? 'eventlab_global';
        $this->provisionTenantDatabase($dbname);
    }

    public function provisionTenantDatabase(string $dbName): void
    {
        $host     = $this->resolveHost();
        $rootUser = $this->secrets['DB_ROOT'] ?? $this->secrets['DB_USER'] ?? 'root';
        $rootPass = $this->secrets['DB_ROOT_PASS'] ?? $this->secrets['DB_PASS'] ?? '';
        $port     = $this->secrets['DB_PORT'] ?? 3306;

        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $rootUser, $rootPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $dbNameEscaped = '`' . str_replace('`', '``', $dbName) . '`';
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbNameEscaped} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $dbUser = $this->secrets['DB_USER'] ?? 'root';
        $dbPass = $this->secrets['DB_PASS'] ?? '';

        if ($rootUser !== $dbUser) {
            $pdo->exec("CREATE USER IF NOT EXISTS '{$dbUser}'@'%' IDENTIFIED BY '{$dbPass}'");
            $pdo->exec("GRANT ALL PRIVILEGES ON {$dbNameEscaped}.* TO '{$dbUser}'@'%'");
            $pdo->exec('FLUSH PRIVILEGES');
        }
    }
}
