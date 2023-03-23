<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Console\Commands\BaseCommand;
use Exception;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class ReconcileCommand.
 */
abstract class ReconcileCommand extends BaseCommand
{
    use ReconcilesRepositories;

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $validator = $this->validator();
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $result = $this->reconcileRepositories($validator->validated());

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    abstract protected function validator(): Validator;
}
