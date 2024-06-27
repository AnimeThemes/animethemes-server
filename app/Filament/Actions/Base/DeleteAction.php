<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Tables\Actions\DeleteAction as DefaultDeleteAction;

/**
 * Class DeleteAction.
 */
class DeleteAction extends DefaultDeleteAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.delete'));

        $this->after(function ($record) {
            ActionLog::modelDeleted($record);
        });
    }
}
