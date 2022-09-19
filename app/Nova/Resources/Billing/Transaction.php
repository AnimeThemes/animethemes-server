<?php

declare(strict_types=1);

namespace App\Nova\Resources\Billing;

use App\Enums\Models\Billing\Service;
use App\Models\Auth\User;
use App\Models\Billing\Transaction as TransactionModel;
use App\Nova\Actions\Repositories\Billing\Transaction\ReconcileTransactionAction;
use App\Nova\Resources\BaseResource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Transaction.
 */
class Transaction extends BaseResource
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
        return __('nova.resources.label.transactions');
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
        return __('nova.resources.singularLabel.transaction');
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
            new Column(TransactionModel::ATTRIBUTE_DESCRIPTION),
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
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), TransactionModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Date::make(__('nova.fields.transaction.date.name'), TransactionModel::ATTRIBUTE_DATE)
                ->sortable()
                ->rules('required')
                ->help(__('nova.fields.transaction.date.help'))
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.fields.transaction.service.name'), TransactionModel::ATTRIBUTE_SERVICE)
                ->options(Service::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(Service::class, false)])
                ->help(__('nova.fields.transaction.service.help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.fields.transaction.description.name'), TransactionModel::ATTRIBUTE_DESCRIPTION)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.fields.transaction.description.help'))
                ->showOnPreview()
                ->filterable(),

            Currency::make(__('nova.fields.transaction.amount.name'), TransactionModel::ATTRIBUTE_AMOUNT)
                ->sortable()
                ->rules('required')
                ->help(__('nova.fields.transaction.amount.help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.fields.transaction.external_id.name'), TransactionModel::ATTRIBUTE_EXTERNAL_ID)
                ->nullable()
                ->sortable()
                ->copyable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.fields.transaction.external_id.help'))
                ->showOnPreview()
                ->filterable(),

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
                (new ReconcileTransactionAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create transaction');
                    }),
            ]
        );
    }
}
