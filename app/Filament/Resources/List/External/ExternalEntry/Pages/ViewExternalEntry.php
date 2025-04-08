<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\ExternalEntry\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\List\External\ExternalEntry;

/**
 * Class ViewExternalEntry.
 */
class ViewExternalEntry extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
