<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Membership\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\MembershipResource;

class ListMemberships extends BaseListResources
{
    protected static string $resource = MembershipResource::class;
}
