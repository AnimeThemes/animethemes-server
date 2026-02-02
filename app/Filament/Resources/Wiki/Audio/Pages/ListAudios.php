<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\AudioResource;
use App\Filament\Tabs\Audio\AudioVideoTab;
use Filament\Schemas\Components\Tabs\Tab;

class ListAudios extends BaseListResources
{
    use HasTabs;

    protected static string $resource = AudioResource::class;

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            AudioVideoTab::class,
        ]);
    }
}
