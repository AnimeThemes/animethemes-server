<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Base;

use App\Actions\Storage\Base\PruneAction;
use App\Console\Commands\Storage\StorageCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Description('Prune stale files from storage')]
abstract class PruneCommand extends StorageCommand
{
    abstract protected function getAction(): PruneAction;

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'hours' => ['required', 'integer'],
        ]);
    }
}
