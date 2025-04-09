<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Anime\Theme;

/**
 * Class ViewTheme.
 */
class ViewTheme extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
