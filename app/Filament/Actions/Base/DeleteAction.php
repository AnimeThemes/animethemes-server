<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Models\Admin\ActionLog;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Actions\DeleteAction as BaseDeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class DeleteAction extends BaseDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.delete'));

        $this->defaultColor('danger');

        $this->icon(Heroicon::Trash);

        $this->requiresConfirmation();

        $this->using(function (Model $record): bool {
            Gate::authorize('delete', $record);

            $result = $record instanceof PlaylistTrack
                ? new DestroyTrackAction()->destroy($record->playlist, $record)
                : $record->delete();

            return (bool) $result;
        });

        $this->after(fn (Model $record): ActionLog => ActionLog::modelDeleted($record));

        $this->authorize(true);
    }
}
