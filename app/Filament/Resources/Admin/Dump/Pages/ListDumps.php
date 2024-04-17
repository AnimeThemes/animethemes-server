<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Dump\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Admin\Dump;

/**
 * Class ListDumps.
 */
class ListDumps extends BaseListResources
{
    protected static string $resource = Dump::class;

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
