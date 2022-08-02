<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

use App\Console\Commands\ReconcileCommand;
use App\Contracts\Repositories\RepositoryInterface;
use App\Rules\Wiki\StorageDirectoryExistsRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Class StorageReconcileCommand.
 */
abstract class StorageReconcileCommand extends ReconcileCommand
{
    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    abstract protected function disk(): string;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  array  $validated
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        array $validated,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        $path = Arr::get($validated, 'path');
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

        return \Illuminate\Support\Facades\Validator::make($this->options(), [
            'path' => ['nullable', 'string', 'regex:/^(?!\/)[\w|\/]+$/', new StorageDirectoryExistsRule($fs)],
        ]);
    }
}
