<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage;

use App\Console\Commands\BaseCommand;
use App\Contracts\Actions\Storage\StorageAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

abstract class StorageCommand extends BaseCommand
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $validator = $this->validator();

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Log::error($error);
                $this->error($error);
            }

            return 1;
        }

        $action = $this->getAction();

        $storageResults = $action->handle();

        $storageResults->toLog();
        $storageResults->toConsole($this);

        $action->then($storageResults);

        $result = $storageResults->toActionResult();

        return $result->hasFailed() ? 1 : 0;
    }

    /**
     * Get the underlying action.
     */
    abstract protected function getAction(): StorageAction;

    /**
     * Get the validator for options.
     */
    abstract protected function validator(): Validator;
}
