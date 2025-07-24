<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Models\Admin\ActionLog;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Actions\DeleteAction as BaseDeleteAction;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends BaseDeleteAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.delete'));

        $this->action(function (Model $record) {
            if ($record instanceof PlaylistTrack) {
                new DestroyTrackAction()->destroy($record->playlist, $record);

                return;
            }

            $record->delete();
        });

        $this->after(fn (Model $record) => ActionLog::modelDeleted($record));
    }
}
