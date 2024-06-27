<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Concerns\Filament\Actions\HasActionLogs;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

/**
 * Class BaseAction.
 *
 * Actions are row actions.
 * They are present in the rows of the table.
 */
abstract class BaseAction extends Action
{
    use HasActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->afterFormValidated(function (BaseAction $action, $livewire) {
            $this->createActionLog($action);
            $livewire->dispatch('updateAllRelationManager');
        });

        $this->after(function ($livewire) {
            $this->finishedLog();
            $livewire->dispatch('updateAllRelationManager');
        });

        $this->modalWidth(MaxWidth::FourExtraLarge);
    }
}