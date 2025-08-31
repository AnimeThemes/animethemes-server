<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\DetachBulkAction as BaseDetachBulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class DetachBulkAction extends BaseDetachBulkAction
{
    use HasActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.detach'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('forceDeleteAny', $model));

        $this->after(function (DetachBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
