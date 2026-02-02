<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\Pages;

use App\Filament\Resources\Auth\PermissionResource;
use App\Filament\Resources\Base\BaseViewResource;

class ViewPermission extends BaseViewResource
{
    protected static string $resource = PermissionResource::class;
}
