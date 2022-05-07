<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation as InvitationModel;
use App\Nova\Actions\Auth\ResendInvitationAction;
use App\Nova\Resources\Resource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Invitation.
 */
class Invitation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = InvitationModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = InvitationModel::ATTRIBUTE_NAME;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, InvitationModel::ATTRIBUTE_EMAIL);
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.auth');
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
        return __('nova.invitations');
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
        return __('nova.invitation');
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
            new Column(InvitationModel::ATTRIBUTE_NAME),
            new Column(InvitationModel::ATTRIBUTE_EMAIL),
        ];
    }

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
            ID::make(__('nova.id'), InvitationModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), InvitationModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.email'), InvitationModel::ATTRIBUTE_EMAIL)
                ->sortable()
                ->copyable()
                ->rules(['required', 'email', 'max:192'])
                ->creationRules(Rule::unique(InvitationModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(InvitationModel::TABLE)
                        ->ignore($request->get('resourceId'), InvitationModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.status'), InvitationModel::ATTRIBUTE_STATUS)
                ->hideWhenCreating()
                ->options(InvitationStatus::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(InvitationStatus::class, false))->__toString()])
                ->showOnPreview()
                ->filterable(),

            Panel::make(__('nova.timestamps'), $this->timestamps())
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
                (new ResendInvitationAction())
                    ->confirmText(__('nova.resend_invitation_confirm_message'))
                    ->confirmButtonText(__('nova.resend'))
                    ->cancelButtonText(__('nova.cancel')),
            ]
        );
    }
}
