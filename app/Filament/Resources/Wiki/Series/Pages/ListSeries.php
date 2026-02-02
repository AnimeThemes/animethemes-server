<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Builder;

class ListSeries extends BaseListResources
{
    protected static string $resource = SeriesResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Series::class);
    }
}
