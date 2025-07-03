<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\List\External;

use App\Actions\Models\List\External\SyncExternalProfileAction;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\List\ExternalProfile;

/**
 * Class SyncExternalProfileHeaderAction.
 */
class SyncExternalProfileHeaderAction extends BaseHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.sync_profile.name'));
        $this->icon(__('filament-icons.actions.models.list.sync_profile'));

        $this->authorize('update', $this->getRecord());

        $this->action(fn (ExternalProfile $record, SyncExternalProfileAction $sync) => $sync->handle($record));
    }
}
