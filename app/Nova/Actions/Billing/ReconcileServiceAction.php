<?php

declare(strict_types=1);

namespace App\Nova\Actions\Billing;

use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Models\Billing\Service;
use App\Nova\Actions\ReconcileAction;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ReconcileServiceAction.
 */
abstract class ReconcileServiceAction extends ReconcileAction
{
    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Select::make(__('nova.service'), 'service')
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->rules(['required', new EnumValue(Service::class, false)])
                ->help(__('nova.billing_service_help')),
        ];
    }

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  ActionFields  $fields
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        ActionFields $fields,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        // Not supported
    }
}
