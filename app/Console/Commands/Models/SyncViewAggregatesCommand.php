<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\Service\SyncViewAggregatesAction;
use App\Console\Commands\BaseCommand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class SyncViewAggregatesCommand.
 */
class SyncViewAggregatesCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:sync-aggregates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes views in the aggregates table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $action = new SyncViewAggregatesAction();

        $result = $action->handle();

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
