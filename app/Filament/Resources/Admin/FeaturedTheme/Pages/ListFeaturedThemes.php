<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\FeaturedTheme\Pages;

use App\Filament\Resources\Admin\FeaturedTheme;
use App\Filament\Resources\Base\BaseListResources;

/**
 * Class ListFeaturedThemes.
 */
class ListFeaturedThemes extends BaseListResources
{
    protected static string $resource = FeaturedTheme::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
