<?php

declare(strict_types=1);

use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use Kyrch\Prohibition\Pivots\ModelProhibition;
use Kyrch\Prohibition\Pivots\ModelSanction;

return [
    'events_enabled' => true,

    'models' => [
        'user' => User::class,
        'prohibition' => Prohibition::class,
        'sanction' => Sanction::class,
        'model_prohibition' => ModelProhibition::class,
        'model_sanction' => ModelSanction::class,
    ],

    'table_names' => [
        'prohibition' => 'prohibitions',
        'sanction' => 'sanctions',
        'sanction_prohibition' => 'sanction_prohibition',
        'model_sanctions' => 'model_sanctions',
        'model_prohibitions' => 'model_prohibitions',
    ],
];
