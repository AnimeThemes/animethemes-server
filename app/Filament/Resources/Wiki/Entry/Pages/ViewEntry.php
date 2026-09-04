<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Entry\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\EntryResource;

class ViewEntry extends BaseViewResource
{
    protected static string $resource = EntryResource::class;
}
