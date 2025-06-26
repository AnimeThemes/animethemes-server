<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Series;
use App\Models\Wiki\Series as SeriesModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListSeries.
 */
class ListSeries extends BaseListResources
{
    protected static string $resource = Series::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, SeriesModel::class);
    }
}
