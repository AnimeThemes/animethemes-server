<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\List\External;

use App\Actions\Models\List\External\SyncExternalProfileAction as SyncExternalProfile;
use App\Enums\Auth\Role;
use App\Filament\Actions\BaseAction;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Auth;

/**
 * Class SyncExternalProfileAction.
 */
class SyncExternalProfileAction extends BaseAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'sync-external-profile';
    }

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

        $this->visible(Auth::user()->hasRole(Role::ADMIN->value));

        $this->action(fn (ExternalProfile $record, SyncExternalProfile $sync) => $sync->handle($record));
    }
}
