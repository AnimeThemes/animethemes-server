<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Base;

use App\Actions\Storage\Base\PruneAction;
use App\Console\Commands\Storage\StorageCommand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

abstract class PruneCommand extends StorageCommand
{
    protected $description = 'Prune stale files from storage';

    abstract protected function getAction(): PruneAction;

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'hours' => ['required', 'integer'],
        ]);
    }
}
