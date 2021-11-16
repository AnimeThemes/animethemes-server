<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation as InvitationModel;
use App\Nova\Actions\Auth\ResendInvitationAction;
use App\Nova\Filters\Auth\InvitationStatusFilter;
use App\Nova\Resources\Resource;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

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
    public static $title = InvitationModel::ATTRIBUTE_EMAIL;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        InvitationModel::ATTRIBUTE_EMAIL,
    ];

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
            ID::make(__('nova.id'), InvitationModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Text::make(__('nova.name'), InvitationModel::ATTRIBUTE_NAME)
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash']),

            Text::make(__('nova.email'), InvitationModel::ATTRIBUTE_EMAIL)
                ->sortable()
                ->rules(['required', 'email', 'max:192'])
                ->creationRules(Rule::unique(InvitationModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(InvitationModel::TABLE)
                        ->ignore($request->resourceId, InvitationModel::ATTRIBUTE_ID)
                        ->__toString()
                ),

            Select::make(__('nova.status'), InvitationModel::ATTRIBUTE_STATUS)
                ->hideWhenCreating()
                ->options(InvitationStatus::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(InvitationStatus::class, false))->__toString()]),

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
                new InvitationStatusFilter(),
            ],
            parent::filters($request)
        );
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function actions(Request $request): array
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
