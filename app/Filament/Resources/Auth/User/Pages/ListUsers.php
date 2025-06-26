<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Auth\User;

/**
 * Class ListUsers.
 */
class ListUsers extends BaseListResources
{
    protected static string $resource = User::class;
}
