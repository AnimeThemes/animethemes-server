<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Resources\Auth\Role;
use App\Filament\Resources\Base\BaseListResources;

/**
 * Class ListRoles.
 */
class ListRoles extends BaseListResources
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
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
