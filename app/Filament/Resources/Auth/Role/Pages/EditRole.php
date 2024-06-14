<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\HeaderActions\Models\Auth\Role\GivePermissionHeaderAction;
use App\Filament\HeaderActions\Models\Auth\Role\RevokePermissionHeaderAction;
use App\Filament\Resources\Auth\Role;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

/**
 * Class EditRole.
 */
class EditRole extends BaseEditResource
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
            [
                ActionGroup::make([
                    GivePermissionHeaderAction::make('give-permission'),

                    RevokePermissionHeaderAction::make('revoke-permission'),
                ]),
            ],
        );
    }
}
