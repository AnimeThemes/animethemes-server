<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Repositories\ReconcileRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class ReconcileCommand.
 */
abstract class ReconcileCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
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

        $sourceRepository = $this->getSourceRepository($validator->validated());
        if ($sourceRepository === null) {
            Log::error('Could not find source repository', $validator->validated());
            $this->error('Could not find source repository');

            return 1;
        }

        $destinationRepository = $this->getDestinationRepository($validator->validated());
        if ($destinationRepository === null) {
            Log::error('Could not find destination repository', $validator->validated());
            $this->error('Could not find destination repository');

            return 1;
        }

        $this->handleFilters($validator->validated(), $sourceRepository, $destinationRepository);

        $action = $this->getAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this);

        return 0;
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    abstract protected function validator(): Validator;

    /**
     * Get source repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    abstract protected function getSourceRepository(array $validated): ?RepositoryInterface;

    /**
     * Get destination repository for action.
     *
     * @param  array  $validated
     * @return RepositoryInterface|null
     */
    abstract protected function getDestinationRepository(array $validated): ?RepositoryInterface;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  array  $validated
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    abstract protected function handleFilters(
        array $validated,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void;

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function getAction(): ReconcileRepositories;
}
