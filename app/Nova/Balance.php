<?php

namespace App\Nova;

use App\Enums\BillingFrequency;
use App\Enums\BillingService;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Panel;

class Balance extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Balance::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'balance_id';

    /**
     * The logical group associated with the resource.
     *
     * @return array|string|null
     */
    public static function group()
    {
        return __('nova.admin');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label()
    {
        return __('nova.balances');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return __('nova.balance');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'balance_id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'balance_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Date::make(__('nova.date'), 'date')
                ->format('YYYY-MM')
                ->pickerFormat('Y-m')
                ->pickerDisplayFormat('Y-m')
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_date_help')),

            Select::make(__('nova.service'), 'service')
                ->options(BillingService::asSelectArray())
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(BillingService::class, false))->__toString())
                ->help(__('nova.billing_service_help')),

            Select::make(__('nova.frequency'), 'frequency')
                ->options(BillingService::asSelectArray())
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('required', (new EnumValue(BillingFrequency::class, false))->__toString())
                ->help(__('nova.balance_frequency_help')),

            Currency::make(__('nova.usage'), 'usage')
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_usage_help')),

            Currency::make(__('nova.balance'), 'balance')
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_balance_help')),
        ];
    }

    /**
     * @return array
     */
    protected function timestamps()
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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\BillingServiceFilter,
            new Filters\BalanceFrequencyFilter,
            new Filters\CreatedStartDateFilter,
            new Filters\CreatedEndDateFilter,
            new Filters\UpdatedStartDateFilter,
            new Filters\UpdatedEndDateFilter,
            new Filters\DeletedStartDateFilter,
            new Filters\DeletedEndDateFilter,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
