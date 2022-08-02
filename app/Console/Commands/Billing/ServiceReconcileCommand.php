<?php

declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Console\Commands\ReconcileCommand;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class ServiceReconcileCommand.
 */
abstract class ServiceReconcileCommand extends ReconcileCommand
{
    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return \Illuminate\Support\Facades\Validator::make($this->arguments(), [
            'service' => ['required', new EnumKey(Service::class)],
        ]);
    }

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
        // Not supported
    }
}
