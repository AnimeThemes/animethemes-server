<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Team
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the team that grants
    | access to Horizon.
    |
    */

    'horizon' => (int) env('HORIZON_TEAM_ID', -1),

    /*
    |--------------------------------------------------------------------------
    | Nova Team
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the team that grants
    | access to Nova.
    |
    */

    'nova' => (int) env('NOVA_TEAM_ID', -1),

    /*
    |--------------------------------------------------------------------------
    | Telescope Team
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the team that grants
    | access to Telescope.
    |
    */

    'telescope' => (int) env('TELESCOPE_TEAM_ID', -1),
];
