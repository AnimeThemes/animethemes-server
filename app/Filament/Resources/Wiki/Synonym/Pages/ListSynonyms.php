<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Synonym\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\SynonymResource;

class ListSynonyms extends BaseListResources
{
    protected static string $resource = SynonymResource::class;
}
