<?php

$allMigrations = [
    'globals'  => [
        // MIGRATION

        '/globals/migrationseeds.sql'  => 'table',
        '/globals/projects.sql'        => 'table',

        '/globals/cache.sql'           => 'table',

        '/globals/notes.sql'           => 'table',

        '/globals/tags.sql'            => 'table',
        '/globals/settings.sql'        => 'table',
        '/globals/templates.sql'       => 'table',
        '/globals/widgets.sql'         => 'table',

        '/globals/accu_attributes.sql' => 'table',

        '/globals/lut_agents.sql'      => 'table',
        '/globals/lut_locations.sql'   => 'table',
        '/globals/lut_referrers.sql'   => 'table',

        '/globals/user_logins.sql'     => 'table',
        '/globals/user_roles.sql'      => 'table',
        '/globals/user_tenants.sql'    => 'table',
        '/globals/login_tokens.sql'    => 'table',

        // SEEDS

        // '/seeds/projects.sql'          => 'seed',

        '/seeds/attributes.sql'        => 'seed',
        '/seeds/notes.sql'             => 'seed',
        '/seeds/settings.sql'          => 'seed',
    ],

    'dynamics' => [
        // MIGRATION

        '/dynamics/prospects.sql'    => 'table',

        '/dynamics/puls_forms.sql'   => 'table',
        '/dynamics/puls_signals.sql' => 'table',
        '/dynamics/puls_visits.sql'  => 'table',
        '/dynamics/puls_events.sql'  => 'table',

        '/dynamics/accu_bits.sql'    => 'table',
        '/dynamics/accu_tupples.sql' => 'table',
        '/dynamics/accu_words.sql'   => 'table',

        '/dynamics/qualifiers.sql'       => 'table',
        '/dynamics/qualifier_panels.sql' => 'table',
        '/dynamics/result_segments.sql'  => 'table',
        '/dynamics/result_scores.sql'    => 'table',

        // '/seeds/prospects.sql'       => 'seed',

        // SEEDS
    ],

    'builders' => [
        // MIGRATION

        '/builders/site_urls.sql'        => 'table',
        '/builders/site_settings.sql'    => 'table',
        '/builders/builder_forms.sql'    => 'table',
        '/builders/builder_sections.sql' => 'table',
        '/builders/builder_pages.sql'    => 'table',

        // SEEDS
    ],
];
