<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Role\Pages;

use App\Filament\Actions\Models\Auth\Role\GivePermissionAction;
use App\Filament\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Auth\Role;
use Filament\Actions\ActionGroup;

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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                GivePermissionAction::make('give-permission'),

                RevokePermissionAction::make('revoke-permission'),
            ]),
        ];
    }
}
