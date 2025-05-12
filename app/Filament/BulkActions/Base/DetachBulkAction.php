<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Tables\Actions\DetachBulkAction as DefaultDetachBulkAction;
use Illuminate\Support\Facades\Auth;

/**
 * Class DetachBulkAction.
 */
class DetachBulkAction extends DefaultDetachBulkAction
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

        $this->label(__('filament.bulk_actions.base.detach'));

        $this->visible(Auth::user()->can('forcedeleteany'));

        $this->after(function (DetachBulkAction $action) {
            foreach ($this->getRecords() as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
