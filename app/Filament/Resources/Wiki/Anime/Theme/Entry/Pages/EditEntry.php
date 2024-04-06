<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages;

use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditEntry.
 */
class EditEntry extends BaseEditResource
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
