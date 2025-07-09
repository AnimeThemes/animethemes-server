<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\DetachBulkAction as BaseDetachBulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * Class DetachBulkAction.
 */
class DetachBulkAction extends BaseDetachBulkAction
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

        $this->visible(fn ($model) => Gate::allows('forceDeleteAny', $model));

        $this->after(function (DetachBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
