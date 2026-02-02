<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\Base\BaseViewResource;

class ViewUser extends BaseViewResource
{
    protected static string $resource = UserResource::class;
}
