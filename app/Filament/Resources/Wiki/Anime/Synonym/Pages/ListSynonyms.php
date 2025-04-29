<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Synonym\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Synonym;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListSynonyms.
 */
class ListSynonyms extends BaseListResources
{
    protected static string $resource = Synonym::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeSynonym::class);
    }
}
