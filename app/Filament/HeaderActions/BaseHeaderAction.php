<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions;

use App\Concerns\Filament\Actions\HasActionLogs;
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

        $this->afterFormValidated(fn (BaseHeaderAction $action) => $this->createActionLog($action));

        $this->after(fn () => $this->finishedLog());

        $this->modalWidth(MaxWidth::FourExtraLarge);
    }
}