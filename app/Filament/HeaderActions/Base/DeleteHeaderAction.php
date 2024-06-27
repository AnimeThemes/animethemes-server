<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\DeleteAction as DefaultDeleteAction;

/**
 * Class DeleteHeaderAction.
 */
class DeleteHeaderAction extends DefaultDeleteAction
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
