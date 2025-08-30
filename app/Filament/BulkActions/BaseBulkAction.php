<?php

declare(strict_types=1);

namespace App\Filament\BulkActions;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\BulkAction;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;

/**
 * Bulk actions are present in the table to perform an action on more than one model at once.
 */
abstract class BaseBulkAction extends BulkAction
{
    use HasActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->before(function (BaseBulkAction $action, Collection $records) {
            foreach ($records as $record) {
                $this->createActionLog($action, $record, false);
            }
        });

        $this->after(fn () => $this->batchFinishedLog());

        $this->modalWidth(Width::FourExtraLarge);

        $this->action(fn (Collection $records, array $data) => $this->handle($records, $data));
    }

    /**
     * @param  Collection  $records
     * @param  array<string, mixed>  $data
     */
    abstract public function handle(Collection $records, array $data): void;
}
