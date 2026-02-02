<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\Base\BaseListResources;

class ListUsers extends BaseListResources
{
    protected static string $resource = UserResource::class;
}
