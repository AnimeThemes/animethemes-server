<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Base;

use App\Actions\Storage\Base\PruneAction;
use App\Console\Commands\Storage\StorageCommand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class PruneCommand.
 */
abstract class PruneCommand extends StorageCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale files from storage';

    /**
     * Get the underlying action.
     *
     * @return PruneAction
     */
    abstract protected function getAction(): PruneAction;

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'hours' => ['required', 'integer'],
        ]);
    }
}
