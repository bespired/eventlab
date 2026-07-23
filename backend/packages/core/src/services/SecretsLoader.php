<?php
namespace EventLab\Core\Services;

/**
 * Loads the secrets configuration from the environment.
 *
 * Resolution order (mirrors the docker-compose mount strategy):
 *   1. /etc/app_secret.php  — Docker-mounted stage file
 *                             (docker-compose maps ./config/secrets/${STAGE:-development}.php here)
 *   2. config/secrets/development.php — local fallback for non-Docker development
 */
class SecretsLoader
{
    public static function load(): array
    {
        $dockerSecret = '/etc/app_secret.php';

        // __DIR__ = backend/packages/core/src  →  ../../../  = backend/
        $projectRoot = realpath(__DIR__ . '/../../../../') . '/';
        $localSecret = $projectRoot . 'config/secrets/development.php';

        if (file_exists($dockerSecret)) {
            return include $dockerSecret;
        }

        if (file_exists($localSecret)) {
            return include $localSecret;
        }

        throw new \RuntimeException(
            'CRITICAL ERROR: No secret configuration file found. ' .
            'Either run via Docker (./config/secrets/\${STAGE}.php is mounted to /etc/app_secret.php) ' .
            'or create a local file at: ' . $localSecret
        );
    }
}
