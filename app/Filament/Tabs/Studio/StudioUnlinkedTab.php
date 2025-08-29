<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Builder;

class StudioUnlinkedTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'studio-unlinked-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.studio.unlinked.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Studio::RELATION_ANIME);
    }

    public function getBadge(): int
    {
        return Studio::query()->whereDoesntHave(Studio::RELATION_ANIME)->count();
    }
}
