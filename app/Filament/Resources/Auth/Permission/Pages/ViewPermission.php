<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\Pages;

use App\Filament\Resources\Auth\Permission;
use App\Filament\Resources\Base\BaseViewResource;

/**
 * Class ViewPermission.
 */
class ViewPermission extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
