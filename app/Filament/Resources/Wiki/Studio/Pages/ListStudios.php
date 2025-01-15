<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Studio;
use App\Filament\Tabs\Studio\Image\StudioCoverLargeTab;
use App\Filament\Tabs\Studio\Image\StudioCoverSmallTab;
use App\Filament\Tabs\Studio\Resource\StudioAnidbResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnilistResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnimePlanetResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioAnnResourceTab;
use App\Filament\Tabs\Studio\Resource\StudioMalResourceTab;
use App\Filament\Tabs\Studio\StudioUnlinkedTab;
use App\Models\Wiki\Studio as StudioModel;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListStudios.
 */
class ListStudios extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Studio::class;

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
            $query->whereIn(StudioModel::ATTRIBUTE_ID, StudioModel::search($search)->take(25)->keys());
        }

        return $query;
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            StudioCoverLargeTab::class,
            StudioCoverSmallTab::class,
            StudioAnidbResourceTab::class,
            StudioAnilistResourceTab::class,
            StudioAnimePlanetResourceTab::class,
            StudioAnnResourceTab::class,
            StudioMalResourceTab::class,
            StudioUnlinkedTab::class,
        ]);
    }
}
