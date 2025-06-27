<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\HeaderActions\Models\Auth\User\GivePermissionHeaderAction;
use App\Filament\HeaderActions\Models\Auth\User\GiveRoleHeaderAction;
use App\Filament\HeaderActions\Models\Auth\User\RevokePermissionHeaderAction;
use App\Filament\HeaderActions\Models\Auth\User\RevokeRoleHeaderAction;
use App\Filament\Resources\Auth\User;
use App\Filament\Resources\Base\BaseViewResource;
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
                GiveRoleHeaderAction::make('give-role'),

                RevokeRoleHeaderAction::make('revoke-role'),

                GivePermissionHeaderAction::make('give-permission'),

                RevokePermissionHeaderAction::make('revoke-permission'),
            ]),
        ];
    }
}
