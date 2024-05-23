<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\Pages;

use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\ExternalResource;

/**
 * Class EditExternalResource.
 */
class EditExternalResource extends BaseEditResource
{
    protected static string $resource = ExternalResource::class;

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
