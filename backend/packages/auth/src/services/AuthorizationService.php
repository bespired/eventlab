<?php

namespace EventLab\Auth\Services;

use EventLab\Auth\Repositories\UserRoleRepository;

class AuthorizationService
{
    private UserRoleRepository $roleRepo;
    private array $env;

    public function __construct(UserRoleRepository $roleRepo, array $env = [])
    {
        $this->roleRepo = $roleRepo;
        $this->env      = $env;
    }

    /**
     * Checks if the request is allowed based on token/roles.
     */
    // Services/AuthorizationService.php
    public function isGranted(?string $token, array|string $roles = [], ?string $tenant = null): bool
    {
        $roles = (array) $roles;

        if (!$token) {
            return false;
        }

        // Parse embedded tenant format ("tenant_a:token_xyz") if present
        if (str_contains($token, ':')) {
            [$tokenTenant, $token] = explode(':', $token, 2);
            $tenant                = $tenant ?? $tokenTenant;
        }

        // 1. Static token checks
        $tokenRoles = ['storage_node', 'external'];
        $matches    = array_intersect($roles, $tokenRoles);

        foreach ($matches as $match) {
            $envKey = strtoupper($match) . '_TOKEN';
            if (isset($this->env[$envKey]) && $this->env[$envKey] === $token) {
                return true;
            }
        }

        // 2. Validate token existence
        if (!$this->roleRepo->isValidToken($token)) {
            return false;
        }

        if (empty($roles)) {
            return true;
        }

        // 3. Fetch roles scoped to tenant
        $userRoles = $this->roleRepo->getUserRoles($token, $tenant ?? '');

        if (in_array('superuser', $userRoles, true)) {
            return true;
        }

        return (bool) array_intersect($roles, $userRoles);
    }
}
