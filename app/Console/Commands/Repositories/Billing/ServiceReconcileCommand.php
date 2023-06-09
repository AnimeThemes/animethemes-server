<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Billing;

use App\Console\Commands\Repositories\ReconcileCommand;
use App\Enums\Models\Billing\Service;
use App\Rules\Api\EnumLocalizedNameRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

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
        return ValidatorFacade::make($this->arguments(), [
            'service' => ['required', new EnumLocalizedNameRule(Service::class)],
        ]);
    }
}
