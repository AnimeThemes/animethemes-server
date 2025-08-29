<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Actions\Models\Aggregate\SyncLikeAggregatesAction;
use App\Console\Commands\BaseCommand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class SyncLikeAggregatesCommand extends BaseCommand
{
    protected $signature = 'like:sync-aggregates';

    protected $description = 'Synchronizes likes in the aggregates table';

    public function handle(): int
    {
        $action = new SyncLikeAggregatesAction();

        $result = $action->handle();

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
