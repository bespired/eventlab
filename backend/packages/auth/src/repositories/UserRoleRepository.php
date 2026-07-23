<?php

namespace EventLab\Auth\Repositories;

use PDO;

class UserRoleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserRoles(string $token, string $tenant): array
    {
        $stmt = $this->pdo->prepare('
            SELECT r.role
            FROM login_tokens lt
            JOIN user_tenants ut ON ut.user = lt.user
            JOIN user_roles r    ON r.prospect = ut.prospect
            WHERE lt.token = :token
              AND ut.tenant = :tenant
        ');

        $stmt->execute([
            'token'  => $token,
            'tenant' => $tenant,
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function isValidToken(string $token): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM login_tokens WHERE token = ?');
        $stmt->execute([$token]);

        return (bool) $stmt->fetchColumn();
    }
}
