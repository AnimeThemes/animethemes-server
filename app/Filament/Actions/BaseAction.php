<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;

/**
 * Class BaseAction.
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
    }
}