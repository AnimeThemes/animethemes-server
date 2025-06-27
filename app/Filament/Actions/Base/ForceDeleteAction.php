<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Actions\Http\Api\List\Playlist\Track\ForceDeleteTrackAction;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Actions\ForceDeleteAction as BaseForceDeleteAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ForceDeleteAction.
 */
class ForceDeleteAction extends BaseForceDeleteAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.forcedelete'));

        $this->action(function (Model $record) {
            if ($record instanceof PlaylistTrack) {
                return new ForceDeleteTrackAction()->forceDelete($record->playlist, $record);
            }

            $record->forceDelete();
        });

        $this->visible(true);
    }
}
