<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Dump\Pages;

use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Admin\Dump;

/**
 * Class ManageDumps.
 */
class ManageDumps extends BaseManageResources
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
        return [];
    }
}
