<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Entry;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Entry;
use Illuminate\Database\Eloquent\Builder;

class EntryVideoTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'entry-videos-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.entry.video.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Entry::RELATION_VIDEOS);
    }
}
