<?php

namespace App\Nova;

use App\Enums\UserType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use SimpleSquid\Nova\Fields\Enum\Enum;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Determine if this resource is available for navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return $request->user()->isAdmin();
    }

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
        return __('nova.users');
    }

    public static function singularLabel()
    {
        return __('nova.user');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name'
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
            ID::make(__('nova.id'), 'id')->sortable(),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Gravatar::make()->maxWidth(50),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('nova.email'), 'email')
                ->readonly(function ($request) {
                    return !$request->user()->isAdmin();
                })
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Enum::make(__('nova.type'), 'type')
                ->attachEnum(UserType::class)
                ->sortable()
                ->readonly(function ($request) {
                    return !$request->user()->isAdmin();
                })
                ->rules('required', new EnumValue(UserType::class, false)),

            Password::make(__('nova.password'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            // Computed fields for 2FA configuration

            Text::make(__('nova.2fa_enabled'), function() {
                return $this->hasTwoFactorEnabled() ? __('nova.enabled') : __('nova.disabled');
            })->exceptOnForms()
                ->canSee(function ($request) {
                    return $request->user()->isAdmin();
                }),

            Text::make(__('nova.two_factor_authentication'), function () {
                if (!$this->hasTwoFactorEnabled()) {
                    return '<a href="' . route('2fa.create') . '">' . __('nova.enable') . '</a>';
                }
                return __('nova.enabled');
            })->asHtml()
                ->onlyOnDetail()
                ->canSee(function ($request) {
                    return $request->user()->is($this);
                }),
        ];
    }

    protected function timestamps() {
        return [
            DateTime::make(__('nova.created_at'), 'created_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.updated_at'), 'updated_at')
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
            new Filters\UserTypeFilter,
            new Filters\TwoFactorAuthenticationEnabledFilter,
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
            (new Actions\GenerateRecoveryCodesAction)
                ->confirmText(__('nova.recovery_codes_generate_confirmation'))
                ->confirmButtonText(__('nova.generate'))
                ->cancelButtonText(__('nova.cancel'))
                ->canSee(function ($request) {
                    return $request->user()->hasTwoFactorEnabled() || $request->user()->isAdmin();
                }),
            (new Actions\DisableTwoFactorAuthenticationAction)
                ->onlyOnDetail()
                ->confirmText(__('nova.2fa_disable_confirmation'))
                ->confirmButtonText(__('nova.disable'))
                ->cancelButtonText(__('nova.cancel'))
                ->canSee(function ($request) {
                    return $request->user()->hasTwoFactorEnabled() || $request->user()->isAdmin();
                }),
        ];
    }
}
