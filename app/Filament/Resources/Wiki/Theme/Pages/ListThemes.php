<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Theme\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\ThemeResource;
use App\Filament\Tabs\Theme\ThemeEntryTab;
use App\Filament\Tabs\Theme\ThemeStaffTab;
use App\Models\Wiki\Theme;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListThemes extends BaseListResources
{
    use HasTabs;

    protected static string $resource = ThemeResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Theme::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            ThemeEntryTab::class,
            ThemeStaffTab::class,
        ]);
    }
}
