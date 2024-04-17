<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Auth\User;

/**
 * Class CreateUser.
 */
class CreateUser extends BaseCreateResource
{
    protected static string $resource = User::class;
}
