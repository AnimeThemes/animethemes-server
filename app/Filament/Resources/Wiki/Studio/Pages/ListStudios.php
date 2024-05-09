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
use Filament\Resources\Components\Tab;

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
