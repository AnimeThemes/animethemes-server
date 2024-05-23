<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Group;

/**
 * Class CreateGroup.
 */
class CreateGroup extends BaseCreateResource
{
    protected static string $resource = Group::class;
}
