<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\RestoreAction as BaseRestoreAction;
use Illuminate\Database\Eloquent\Model;

class RestoreAction extends BaseRestoreAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.restore'));

        $this->after(fn (Model $record) => ActionLog::modelRestored($record));
    }
}
