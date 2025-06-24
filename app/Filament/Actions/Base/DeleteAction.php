<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\DeleteAction as BaseDeleteAction;

/**
 * Class DeleteAction.
 */
class DeleteAction extends BaseDeleteAction
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
