<?php

declare(strict_types=1);

use App\Enums\Rules\ModerationService;

return [

    /*
    |--------------------------------------------------------------------------
    | Moderation Service
    |--------------------------------------------------------------------------
    |
    | These values determine which service is used to assist with content filtering
    | from user-sourced input. By default, no moderation is applied. We want
    | to make this feature opt-in since it relies on an external service.
    |
    */

    'moderation_service' => env('MODERATION_SERVICE', ModerationService::NONE),
];
