<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\ExternalEntry\Pages;

use App\Filament\Resources\List\External\ExternalEntry;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditExternalEntry.
 */
class EditExternalEntry extends BaseEditResource
{
    protected static string $resource = ExternalEntry::class;

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
