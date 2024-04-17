<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Auth\Role;

/**
 * Class ViewRole.
 */
class ViewRole extends BaseViewResource
{
    protected static string $resource = Role::class;

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
