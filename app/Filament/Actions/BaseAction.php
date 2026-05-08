<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Concerns\Filament\HasActivityLogs;
use App\Enums\Models\Admin\ActivityStatus;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseAction extends Action
{
    use HasActivityLogs;

    public static function getDefaultName(): ?string
    {
        return Str::random();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->modal();

        $this->before(function (BaseAction $action, mixed $livewire): void {
            if ($livewire instanceof BaseRelationManager) {
                $this->createActivityLog($action, $livewire->getOwnerRecord());
                $livewire->dispatch('updateAllRelationManager');
            } elseif (($record = $this->getRecord()) instanceof Model) {
                $this->createActivityLog($action, $record);
            }
        });

        $this->after(function (mixed $livewire, BaseAction $action): void {
            if ($action instanceof ShouldQueue) {
                return;
            }

            $this->activity?->update([
                'status' => ActivityStatus::FINISHED,
                'finished_at' => now(),
            ]);

            $this->finishedLog();

            if ($livewire instanceof BaseViewResource || $livewire instanceof BaseRelationManager) {
                $livewire->dispatch('updateAllRelationManager');
            }
        });

        $this->modalWidth(Width::FourExtraLarge);
    }
}
