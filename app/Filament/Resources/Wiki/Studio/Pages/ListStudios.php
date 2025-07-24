<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Studio;
use App\Filament\Tabs\Studio\Image\StudioLargeCoverTab;
use App\Filament\Tabs\Studio\Image\StudioSmallCoverTab;
use App\Filament\Tabs\Studio\Resource\StudioAnidbResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnilistResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnimePlanetResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnnResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioMalResourceTab;
use App\Filament\Tabs\Studio\StudioUnlinkedTab;
use App\Models\Wiki\Studio as StudioModel;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStudios extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Studio::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, StudioModel::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            StudioLargeCoverTab::class,
            StudioSmallCoverTab::class,
            StudioAnidbResourceTab::class,
            StudioAnilistResourceTab::class,
            StudioAnimePlanetResourceTab::class,
            StudioAnnResourceTab::class,
            StudioMalResourceTab::class,
            StudioUnlinkedTab::class,
        ]);
    }
}
