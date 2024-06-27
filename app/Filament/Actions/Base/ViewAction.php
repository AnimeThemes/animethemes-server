<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Tables\Actions\ViewAction as DefaultViewAction;

/**
 * Class ViewAction.
 */
class ViewAction extends DefaultViewAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.view'));
    }
}
