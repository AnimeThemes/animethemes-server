<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Actions\Models\Auth\User\GivePermissionAction;
use App\Filament\Actions\Models\Auth\User\GiveRoleAction;
use App\Filament\Actions\Models\Auth\User\RevokePermissionAction;
use App\Filament\Actions\Models\Auth\User\RevokeRoleAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Auth\User;
use Filament\Actions\ActionGroup;

/**
 * Class ViewUser.
 */
class ViewUser extends BaseViewResource
{
    protected static string $resource = User::class;

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
                GiveRoleAction::make('give-role'),

                RevokeRoleAction::make('revoke-role'),

                GivePermissionAction::make('give-permission'),

                RevokePermissionAction::make('revoke-permission'),
            ]),
        ];
    }
}
