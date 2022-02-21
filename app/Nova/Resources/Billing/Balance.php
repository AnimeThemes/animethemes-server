<?php

declare(strict_types=1);

namespace App\Nova\Resources\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance as BalanceModel;
use App\Nova\Filters\Billing\Balance\BalanceFrequencyFilter;
use App\Nova\Filters\Billing\ServiceFilter;
use App\Nova\Resources\Resource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Panel;

/**
 * Class Balance.
 */
class Balance extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = BalanceModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = BalanceModel::ATTRIBUTE_ID;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.billing');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.balances');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.balance');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        BalanceModel::ATTRIBUTE_ID,
    ];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function usesScout(): bool
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), BalanceModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Date::make(__('nova.date'), BalanceModel::ATTRIBUTE_DATE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_date_help')),

            Select::make(__('nova.service'), BalanceModel::ATTRIBUTE_SERVICE)
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(Service::class, false))->__toString()])
                ->help(__('nova.billing_service_help')),

            Select::make(__('nova.frequency'), BalanceModel::ATTRIBUTE_FREQUENCY)
                ->options(BalanceFrequency::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(BalanceFrequency::class, false))->__toString()])
                ->help(__('nova.balance_frequency_help')),

            Currency::make(__('nova.usage'), BalanceModel::ATTRIBUTE_USAGE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_usage_help')),

            Currency::make(__('nova.balance'), BalanceModel::ATTRIBUTE_BALANCE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.balance_balance_help')),

            AuditableLog::make(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new ServiceFilter(),
                new BalanceFrequencyFilter(),
            ],
            parent::filters($request)
        );
    }
}
