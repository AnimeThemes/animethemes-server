<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\RestoreBulkAction as BaseRestoreBulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class RestoreBulkAction extends BaseRestoreBulkAction
{
    use HasActionLogs;

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.restore'));

        $this->visible(fn (string $model) => Gate::allows('restoreAny', $model));

        $this->after(function (RestoreBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
