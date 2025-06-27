<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;

/**
 * Class BaseHeaderAction.
 *
 * Header actions are present at the top of the edit/view model page.
 */
abstract class BaseHeaderAction extends Action
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

        $this->before(function (BaseHeaderAction $action, $livewire) {
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
