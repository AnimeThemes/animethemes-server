<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Synonym\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Synonym;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Builder;

class ListSynonyms extends BaseListResources
{
    protected static string $resource = Synonym::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeSynonym::class);
    }
}
