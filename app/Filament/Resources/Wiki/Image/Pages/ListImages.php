<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Image;

/**
 * Class ListImages.
 */
class ListImages extends BaseListResources
{
    protected static string $resource = Image::class;

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
}
