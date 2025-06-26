<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\Pages;

use Filament\Schemas\Components\Tabs\Tab;
use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Image;
use App\Filament\Tabs\Image\ImageUnlinkedTab;

/**
 * Class ListImages.
 */
class ListImages extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Image::class;

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            ImageUnlinkedTab::class,
        ]);
    }
}
