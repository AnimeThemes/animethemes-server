<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Theme\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\ThemeResource;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Builder;

class ListThemes extends BaseListResources
{
    protected static string $resource = ThemeResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Theme::class);
    }
}
