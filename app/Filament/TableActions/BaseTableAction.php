<?php

declare(strict_types=1);

namespace App\Filament\TableActions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

/**
 * Class BaseTableAction.
 *
 * It is an action related to the table or an owner record of a relation manager.
 * Don't confuse it with the header actions of an individual model.
 */
abstract class BaseTableAction extends Action
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

        $this->before(function ($livewire, BaseTableAction $action) {
            if ($livewire instanceof BaseRelationManager) {
                $this->createActionLog($action, $livewire->getOwnerRecord());
                $livewire->dispatch('updateAllRelationManager');
            }
        });

        $this->after(function ($livewire) {
            if ($livewire instanceof BaseRelationManager) {
                $this->finishedLog();
                $livewire->dispatch('updateAllRelationManager');
            }
        });

        $this->modalWidth(MaxWidth::FourExtraLarge);

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    abstract public function handle(array $fields): void;
}
