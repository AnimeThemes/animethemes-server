<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;

/**
 * Class ViewEntry.
 */
class ViewEntry extends BaseViewResource
{
    protected static string $resource = Entry::class;

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
