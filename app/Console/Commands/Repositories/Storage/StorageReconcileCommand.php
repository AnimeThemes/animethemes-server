<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage;

use App\Console\Commands\Repositories\ReconcileCommand;
use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisk;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class StorageReconcileCommand.
 */
abstract class StorageReconcileCommand extends ReconcileCommand implements InteractsWithDisk
{
    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @param  array  $data
     * @return void
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository,
        array $data = []
    ): void {
        parent::handleFilters($sourceRepository, $destinationRepository, $data);

        $path = Arr::get($data, 'path');
        if ($path !== null) {
            $sourceRepository->handleFilter('path', $path);
            $destinationRepository->handleFilter('path', $path);
        }
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        $fs = Storage::disk($this->disk());

        return ValidatorFacade::make($this->options(), [
            'path' => ['nullable', 'string', 'doesnt_start_with:/', new StorageDirectoryExistsRule($fs)],
        ]);
    }
}
