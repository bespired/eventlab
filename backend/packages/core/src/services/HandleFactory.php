<?php

namespace EventLab\Core\Services;

use EventLab\Core\Support\Base62Converter;

/**
 * Handles creation of unique identifiers (handles) for database records.
 * Replaces the HandleCreate trait.
 */
class HandleFactory
{
    private array $tables = [
        'accu_attributes' => 'aa',
        'accu_bits'       => 'ab',
        'accu_tupples'    => 'at',
        'accu_words'      => 'aw',
        'cache'           => 'cc',
        'lut_agents'      => 'la',
        'lut_locations'   => 'll',
        'lut_referrers'   => 'lr',
        'notes'           => 'nt',
        'prospects'       => 'pp',
        'puls_events'     => 'pe',
        'puls_forms'      => 'pf',
        'puls_signals'    => 'ps',
        'puls_visits'     => 'pv',
        'projects'        => 'pr',
        'settings'        => 'se',
        'tags'            => 'tg',
        'login_tokens'    => 'tt',
        'templates'       => 'tp',
        'unknown'         => 'un',
        'user_logins'     => 'ul',
        'user_roles'      => 'ur',
        'user_tenants'    => 'ut',

        'widgets'         => 'wd',
        'elmi'            => 'el',
    ];

    private Base62Converter $converter;

    public function __construct(Base62Converter $converter)
    {
        $this->converter = $converter;
    }

    public function create(string $table = 'unknown', string $tenant = '', ?string $idx = null): string
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

        $tableMnemonic = $this->tables[$table] ?? 'un';
        $tenantFixed   = substr('00' . $tenant, -2);
        $random        = substr(str_shuffle($pool), 0, 5);

        $base   = $tenantFixed . $tableMnemonic;
        $number = $idx ? substr($idx . $random, -5) : $random;
        $date   = $this->converter->encode(time());

        if ($table === 'user_logins') {
            return "usrt-{$date}-{$number}";
        }

        return "{$base}-{$date}-{$number}";
    }
}
