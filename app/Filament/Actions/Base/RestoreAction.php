<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class RestoreAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'restore';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.restore'));
        $this->modalHeading(fn (): string => __('filament-actions::restore.single.modal.heading', ['label' => $this->getRecordTitle()]));
        $this->modalSubmitActionLabel(__('filament-actions::restore.single.modal.actions.restore.label'));

        $this->successNotificationTitle(__('filament-actions::restore.single.notifications.restored.title'));

        $this->defaultColor('gray');

        $this->tableIcon(Heroicon::ArrowUturnLeft);
        $this->groupedIcon(Heroicon::ArrowUturnLeft);
        $this->modalIcon(Heroicon::OutlinedArrowUturnLeft);

        $this->requiresConfirmation();

        $this->action(function (Model $record): void {
            if (! method_exists($record, 'restore')) {
                $this->failure();

                return;
            }

            $result = $this->process(static fn (): ?bool => $record->restore());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->after(fn (Model $record) => ActionLog::modelRestored($record));
    }
}
