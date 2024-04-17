<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Pages;

use App\Filament\Resources\Wiki\Anime\Theme;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditTheme.
 */
class EditTheme extends BaseEditResource
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
