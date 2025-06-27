<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
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
        $this->failure();

        $this->before(function (BaseAction $action) {
            $this->createActionLog($action);
        });

        $this->after(function () {
            $this->finishedLog();
        });

        $this->modalWidth(MaxWidth::FourExtraLarge);
    }
}
