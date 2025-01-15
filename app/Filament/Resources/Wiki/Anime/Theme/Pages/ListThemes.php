<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListThemes.
 */
class ListThemes extends BaseListResources
{
    protected static string $resource = Theme::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);

        if (filled($search = $this->getTableSearch())) {
            $search = preg_replace('/[^A-Za-z0-9 ]/', '', $search);
            $query->whereIn(AnimeTheme::ATTRIBUTE_ID, AnimeTheme::search($search)->take(25)->keys());
        }

        return $query;
    }
}
