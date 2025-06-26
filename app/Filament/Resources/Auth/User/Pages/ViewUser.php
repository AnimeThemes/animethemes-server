<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Auth\User;

/**
 * Class ViewUser.
 */
class ViewUser extends BaseViewResource
{
    protected static string $resource = User::class;
}
