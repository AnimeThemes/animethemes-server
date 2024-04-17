<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\FeaturedTheme\Pages;

use App\Filament\Resources\Admin\FeaturedTheme;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditFeaturedTheme.
 */
class EditFeaturedTheme extends BaseEditResource
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
