<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class BaseAction.
 */
abstract class BaseAction extends Action
{
    use HasActionLogs;

    /**
     * A random string is generated so the action always has a name.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return Str::random();
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->before(function (BaseAction $action, $livewire) {
            if ($livewire instanceof BaseRelationManager) {
                $this->createActionLog($action, $livewire->getOwnerRecord());
                $livewire->dispatch('updateAllRelationManager');
            } else if (($record = $this->getRecord()) instanceof Model) {
                $this->createActionLog($action, $record);
            }
        });

        $this->after(function ($livewire) {
            $this->finishedLog();

            if ($livewire instanceof BaseRelationManager) {
                $livewire->dispatch('updateAllRelationManager');
            }
        });

        $this->modalWidth(Width::FourExtraLarge);
    }
}
