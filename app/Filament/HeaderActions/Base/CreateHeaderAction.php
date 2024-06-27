<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use Filament\Actions\CreateAction as DefaultCreateAction;

/**
 * Class CreateHeaderAction.
 */
class CreateHeaderAction extends DefaultCreateAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
