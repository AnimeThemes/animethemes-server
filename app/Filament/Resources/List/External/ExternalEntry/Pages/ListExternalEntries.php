<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\ExternalEntry\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\External\ExternalEntry;

class ListExternalEntries extends BaseListResources
{
    protected static string $resource = ExternalEntry::class;

    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
