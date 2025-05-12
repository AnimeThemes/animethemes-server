<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Tables\Actions\RestoreBulkAction as DefaultRestoreBulkAction;
use Illuminate\Support\Facades\Auth;

/**
 * Class RestoreBulkAction.
 */
class RestoreBulkAction extends DefaultRestoreBulkAction
{
    use HasActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.restore'));

        $this->visible(Auth::user()->can('restoreany'));

        $this->after(function (RestoreBulkAction $action) {
            foreach ($this->getRecords() as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
