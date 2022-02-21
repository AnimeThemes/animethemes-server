<?php

declare(strict_types=1);

namespace App\Nova\Resources\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Billing\Transaction as TransactionModel;
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
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class Transaction.
 */
class Transaction extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = TransactionModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = TransactionModel::ATTRIBUTE_DESCRIPTION;

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
        return __('nova.transactions');
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
        return __('nova.transaction');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        TransactionModel::ATTRIBUTE_ID,
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), TransactionModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Date::make(__('nova.date'), TransactionModel::ATTRIBUTE_DATE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.transaction_date_help')),

            Select::make(__('nova.service'), TransactionModel::ATTRIBUTE_SERVICE)
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(Service::class, false))->__toString()])
                ->help(__('nova.billing_service_help')),

            Text::make(__('nova.description'), TransactionModel::ATTRIBUTE_DESCRIPTION)
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.transaction_description_help')),

            Currency::make(__('nova.amount'), TransactionModel::ATTRIBUTE_AMOUNT)
                ->sortable()
                ->rules('required')
                ->help(__('nova.transaction_amount_help')),

            Text::make(__('nova.external_id'), TransactionModel::ATTRIBUTE_EXTERNAL_ID)
                ->nullable()
                ->sortable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.transaction_external_id_help')),

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
            ],
            parent::filters($request)
        );
    }
}
