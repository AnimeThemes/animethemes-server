<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\DeleteBulkAction as BaseDeleteBulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class DeleteBulkAction extends BaseDeleteBulkAction
{
    use HasActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.delete'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('forceDeleteAny', $model));

        $this->after(function (DeleteBulkAction $action, Collection $records): void {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
