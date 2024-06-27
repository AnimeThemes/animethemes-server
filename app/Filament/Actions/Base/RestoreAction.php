<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Tables\Actions\RestoreAction as DefaultRestoreAction;

/**
 * Class RestoreAction.
 */
class RestoreAction extends DefaultRestoreAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.restore'));

        $this->after(function ($record) {
            ActionLog::modelRestored($record);
        });
    }
}
