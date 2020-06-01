<?php

namespace App\Nova;

use App\Enums\InvitationStatus;
use App\Enums\UserType;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;

class Invitation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Invitation::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'email';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static function group() {
        return __('nova.admin');
    }

    public static function label()
    {
        return __('nova.invitations');
    }

    public static function singularLabel()
    {
        return __('nova.invitation');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'email',
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
            ID::make(__('nova.id'), 'invitation_id')->sortable(),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules('required', 'max:255', 'alpha_dash'),

            Text::make(__('nova.email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:invitation,email')
                ->updateRules('unique:invitation,email,{{resourceId}}'),

            Enum::make(__('nova.type'), 'type')
                ->attachEnum(UserType::class)
                ->sortable()
                ->rules('required', new EnumValue(UserType::class, false)),

            Enum::make(__('nova.status'), 'status')
                ->hideWhenCreating()
                ->attachEnum(InvitationStatus::class)
                ->sortable()
                ->rules('required', new EnumValue(InvitationStatus::class, false)),

                AuditableLog::make(),
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
            new Filters\UserTypeFilter,
            new Filters\InvitationStatusFilter,
            new Filters\RecentlyCreatedFilter,
            new Filters\RecentlyUpdatedFilter
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
        return [
            (new Actions\ResendInvitationAction)
                ->confirmText(__('nova.resend_invitation_confirm_message'))
                ->confirmButtonText(__('nova.resend'))
                ->cancelButtonText(__('nova.cancel')),
        ];
    }
}
