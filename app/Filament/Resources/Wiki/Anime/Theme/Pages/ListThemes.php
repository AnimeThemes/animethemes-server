<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Theme;

/**
 * Class ListThemes.
 */
class ListThemes extends BaseListResources
{
    protected static string $resource = Theme::class;

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
