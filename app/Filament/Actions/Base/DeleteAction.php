<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Models\Admin\ActionLog;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.delete'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.single.notifications.deleted.title'));

        $this->defaultColor('danger');

        $this->tableIcon(Heroicon::Trash);
        $this->groupedIcon(Heroicon::Trash);

        $this->requiresConfirmation();

        $this->modalIcon(Heroicon::OutlinedTrash);

        $this->keyBindings(['mod+d']);

        $this->action(function (Model $record): void {
            $result = $record instanceof PlaylistTrack
                ? new DestroyTrackAction()->destroy($record->playlist, $record)
                : $record->delete();

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->after(fn (Model $record) => ActionLog::modelDeleted($record));
    }
}
