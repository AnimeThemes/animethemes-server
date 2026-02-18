<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Synonym\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\SynonymResource;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Builder;

class ListSynonyms extends BaseListResources
{
    protected static string $resource = SynonymResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Synonym::class);
    }
}
