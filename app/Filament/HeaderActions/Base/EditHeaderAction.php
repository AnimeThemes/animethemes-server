<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\EditAction as DefaultEditAction;

/**
 * Class EditHeaderAction.
 */
class EditHeaderAction extends DefaultEditAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.edit'));
        $this->icon(__('filament-icons.actions.base.edit'));
        $this->after(fn ($record) => ActionLog::modelUpdated($record));
    }
}
