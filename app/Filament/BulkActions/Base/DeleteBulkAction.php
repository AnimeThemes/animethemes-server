<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class DeleteBulkAction.
 */
class DeleteBulkAction extends \Filament\Actions\DeleteBulkAction
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

        $this->label(__('filament.bulk_actions.base.delete'));

        $this->visible(fn ($model) => Auth::user()->can('forcedeleteany', $model));

        $this->after(function (DeleteBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
