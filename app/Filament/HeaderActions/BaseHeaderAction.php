<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions;

use Filament\Actions\Action;

/**
 * Class BaseHeaderAction.
 */
abstract class BaseHeaderAction extends Action
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