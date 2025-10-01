<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\List\External;

use App\Actions\Models\List\External\SyncExternalProfileAction as SyncExternalProfile;
use App\Enums\Auth\Role;
use App\Filament\Actions\BaseAction;
use App\Models\List\ExternalProfile;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class SyncExternalProfileAction extends BaseAction
{
    public static function getDefaultName(): ?string
    {
        return 'sync-external-profile';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.sync_profile.name'));
        $this->icon(Heroicon::OutlinedArrowPath);

        $this->visible(Auth::user()->hasRole(Role::ADMIN->value));

        $this->action(fn (ExternalProfile $record, SyncExternalProfile $sync): ExternalProfile => $sync->handle($record));
    }
}
