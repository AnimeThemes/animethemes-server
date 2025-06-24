<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Illuminate\Support\Collection;

/**
 * Class RestoreBulkAction.
 */
class RestoreBulkAction extends \Filament\Actions\RestoreBulkAction
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

        $this->after(function (RestoreBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record);
                $this->finishedLog();
            }
        });
    }
}
