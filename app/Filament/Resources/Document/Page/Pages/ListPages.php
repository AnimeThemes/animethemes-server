<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document\Page\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Document\PageResource;

class ListPages extends BaseListResources
{
    protected static string $resource = PageResource::class;
}
