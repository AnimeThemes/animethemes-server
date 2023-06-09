<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Billing;

use App\Enums\Models\Billing\Service;
use App\Nova\Actions\Repositories\ReconcileAction;
use Illuminate\Validation\Rules\Enum;
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
            Select::make(__('nova.actions.repositories.service.fields.service.name'), 'service')
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => Service::tryFrom($enumValue)?->localize())
                ->rules(['required', new Enum(Service::class)])
                ->help(__('nova.actions.repositories.service.fields.service.help')),
        ];
    }
}
