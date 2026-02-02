<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\ActionLog\Pages;

use App\Filament\Resources\Admin\ActionLogResource;
use App\Filament\Resources\Base\BaseManageResources;

class ManageActionLogs extends BaseManageResources
{
    protected static string $resource = ActionLogResource::class;
}
