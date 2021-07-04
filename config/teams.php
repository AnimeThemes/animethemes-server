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

    'horizon' => env('HORIZON_TEAM_ID'),

    /*
    |--------------------------------------------------------------------------
    | Nova Team
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the team that grants
    | access to Nova.
    |
    */

    'nova' => env('NOVA_TEAM_ID'),

    /*
    |--------------------------------------------------------------------------
    | Telescope Team
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the team that grants
    | access to Telescope.
    |
    */

    'telescope' => env('TELESCOPE_TEAM_ID'),
];
