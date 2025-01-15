<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\Pages;

use App\Filament\HeaderActions\Models\List\External\SyncExternalProfileHeaderAction;
use App\Filament\Resources\List\ExternalProfile;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

/**
 * Class EditExternalProfile.
 */
class EditExternalProfile extends BaseEditResource
{
    protected static string $resource = ExternalProfile::class;

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
                SyncExternalProfileHeaderAction::make('sync-profile'),
            ]),
        ];
    }
}
