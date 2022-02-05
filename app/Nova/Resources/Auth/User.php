<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Models\Auth\User as UserModel;
use App\Nova\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class User.
 */
class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = UserModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = UserModel::ATTRIBUTE_NAME;

    /**
     * Determine if this resource is available for navigation.
     *
     * @param  Request  $request
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function availableForNavigation(Request $request): bool
    {
        $user = $request->user();

        return $user->hasCurrentTeamPermission('user:read');
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
        return __('nova.users');
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
        return __('nova.user');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        UserModel::ATTRIBUTE_NAME,
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
            ID::make(__('nova.id'), UserModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Gravatar::make()->maxWidth(50),

            Text::make(__('nova.name'), UserModel::ATTRIBUTE_NAME)
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash']),

            Text::make(__('nova.email'), UserModel::ATTRIBUTE_EMAIL)
                ->sortable()
                ->rules(['required', 'email', 'max:192'])
                ->creationRules(Rule::unique(UserModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(UserModel::TABLE)
                        ->ignore($request->get('resourceId'), UserModel::ATTRIBUTE_ID)
                        ->__toString()
                ),
        ];
    }
}
