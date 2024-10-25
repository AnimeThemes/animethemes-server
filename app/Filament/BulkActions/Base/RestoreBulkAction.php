<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Tables\Actions\RestoreBulkAction as DefaultRestoreBulkAction;

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

        $this->authorize('forcedeleteany');

        $this->after(function (RestoreBulkAction $action) {
            foreach ($this->getRecords() as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
