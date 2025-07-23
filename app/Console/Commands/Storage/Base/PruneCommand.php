<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Base;

use App\Actions\Storage\Base\PruneAction;
use App\Console\Commands\Storage\StorageCommand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

abstract class PruneCommand extends StorageCommand
{
    /**
     * The console command description.
     */
    protected $description = 'Prune stale files from storage';

    /**
     * Get the underlying action.
     */
    abstract protected function getAction(): PruneAction;

    /**
     * Get the validator for options.
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'hours' => ['required', 'integer'],
        ]);
    }
}
