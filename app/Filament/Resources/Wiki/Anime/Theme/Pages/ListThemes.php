<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Builder;

class ListThemes extends BaseListResources
{
    protected static string $resource = Theme::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeTheme::class);
    }
}
