<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Theme;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Builder;

class ThemeEntryTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'theme-entry-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.theme.entry.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Theme::RELATION_ENTRIES);
    }
}
