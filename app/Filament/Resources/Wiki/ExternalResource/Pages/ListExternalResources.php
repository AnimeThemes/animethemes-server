<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\ExternalResource;
use App\Filament\Tabs\ExternalResource\ExternalResourceUnlinkedTab;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Class ListExternalResources.
 */
class ListExternalResources extends BaseListResources
{
    use HasTabs;

    protected static string $resource = ExternalResource::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            ExternalResourceUnlinkedTab::class,
        ]);
    }
}
