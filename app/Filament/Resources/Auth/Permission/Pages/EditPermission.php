<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\Pages;

use App\Filament\Resources\Auth\Permission;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditPermission.
 */
class EditPermission extends BaseEditResource
{
    protected static string $resource = Permission::class;

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
