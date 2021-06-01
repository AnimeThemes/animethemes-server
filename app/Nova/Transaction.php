<?php

declare(strict_types=1);

namespace App\Nova;

use App\Enums\Billing\Service;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class Transaction
 * @package App\Nova
 */
class Transaction extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Billing\Transaction::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'description';

    /**
     * The logical group associated with the resource.
     *
     * @return array|string|null
     */
    public static function group(): array|string|null
    {
        return __('nova.billing');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label(): array|string|null
    {
        return __('nova.transactions');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel(): array|string|null
    {
        return __('nova.transaction');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'transaction_id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'transaction_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Date::make(__('nova.date'), 'date')
                ->sortable()
                ->rules('required')
                ->help(__('nova.transaction_date_help')),

            Select::make(__('nova.service'), 'service')
                ->options(Service::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(Service::class, false))->__toString())
                ->help(__('nova.billing_service_help')),

            Text::make(__('nova.description'), 'description')
                ->sortable()
                ->rules('required', 'max:192')
                ->help(__('nova.transaction_description_help')),

            Currency::make(__('nova.amount'), 'amount')
                ->sortable()
                ->rules('required')
                ->help(__('nova.transaction_amount_help')),

            Number::make(__('nova.external_id'), 'external_id')
                ->nullable()
                ->sortable()
                ->rules('nullable', 'integer')
                ->help(__('nova.transaction_external_id_help')),
        ];
    }

    /**
     * @return array
     */
    protected function timestamps(): array
    {
        return [
            DateTime::make(__('nova.created_at'), 'created_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.updated_at'), 'updated_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.deleted_at'), 'deleted_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [
            new Filters\BillingServiceFilter(),
            new Filters\CreatedStartDateFilter(),
            new Filters\CreatedEndDateFilter(),
            new Filters\UpdatedStartDateFilter(),
            new Filters\UpdatedEndDateFilter(),
            new Filters\DeletedStartDateFilter(),
            new Filters\DeletedEndDateFilter(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }
}
