<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Theme;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Builder;

class ThemeStaffTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'theme-staff-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.theme.staff.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Theme::RELATION_STAFF);
    }
}
