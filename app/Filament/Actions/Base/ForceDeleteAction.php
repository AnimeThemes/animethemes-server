<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.forcedelete'));
        $this->modalHeading(fn (): string => __('filament-actions::force-delete.single.modal.heading', ['label' => $this->getRecordTitle()]));
        $this->modalSubmitActionLabel(__('filament-actions::force-delete.single.modal.actions.delete.label'));

        $this->defaultColor('danger');

        $this->tableIcon(Heroicon::Trash);
        $this->groupedIcon(Heroicon::Trash);
        $this->modalIcon(Heroicon::OutlinedTrash);

        $this->requiresConfirmation();

        $this->action(function (): void {
            $result = $this->process(static fn (Model $record): ?bool => $record->forceDelete());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(fn (string $model) => $model::isSoftDeletable());
    }
}
