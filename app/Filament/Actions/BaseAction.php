<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseAction extends Action
{
    use HasActionLogs;

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
                $this->createActionLog($action, $livewire->getOwnerRecord());
                $livewire->dispatch('updateAllRelationManager');
            } elseif (($record = $this->getRecord()) instanceof Model) {
                $this->createActionLog($action, $record);
            }
        });

        $this->after(function (mixed $livewire, BaseAction $action): void {
            if ($action instanceof ShouldQueue) {
                return;
            }

            $this->finishedLog();

            if ($livewire instanceof BaseViewResource || $livewire instanceof BaseRelationManager) {
                $livewire->dispatch('updateAllRelationManager');
            }
        });

        $this->modalWidth(Width::FourExtraLarge);
    }
}
