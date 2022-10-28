<?php

declare(strict_types=1);

namespace App\Nova\Resources\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Models\Billing\Balance as BalanceModel;
use App\Nova\Actions\Repositories\Billing\Balance\ReconcileBalanceAction;
use App\Nova\Resources\BaseResource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Balance.
 */
class Balance extends BaseResource
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
        return __('nova.resources.group.billing');
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
        return __('nova.resources.label.balances');
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
        return __('nova.resources.singularLabel.balance');
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(BalanceModel::ATTRIBUTE_ID),
        ];
    }

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
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), BalanceModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Date::make(__('nova.fields.balance.date.name'), BalanceModel::ATTRIBUTE_DATE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.fields.balance.date.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.balance.service.name'), BalanceModel::ATTRIBUTE_SERVICE)
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(Service::class, false)])
                ->help(__('nova.fields.balance.service.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.balance.frequency.name'), BalanceModel::ATTRIBUTE_FREQUENCY)
                ->options(BalanceFrequency::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(BalanceFrequency::class, false)])
                ->help(__('nova.fields.balance.frequency.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Currency::make(__('nova.fields.balance.usage.name'), BalanceModel::ATTRIBUTE_USAGE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.fields.balance.usage.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Currency::make(__('nova.fields.balance.balance.name'), BalanceModel::ATTRIBUTE_BALANCE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.fields.balance.balance.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return array_merge(
            parent::actions($request),
            [
                (new ReconcileBalanceAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', BalanceModel::class),
            ]
        );
    }
}
