<?php

declare(strict_types=1);

namespace App\Filament\Actions;

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
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->modalWidth(MaxWidth::FourExtraLarge);
    }
}