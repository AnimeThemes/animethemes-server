<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\ExternalEntry\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\External\ExternalEntry;

/**
 * Class ListExternalEntries.
 */
class ListExternalEntries extends BaseListResources
{
    protected static string $resource = ExternalEntry::class;

    /**
     * Get the header actions available.
     *
     * @return array<int, \Filament\Actions\Action>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
