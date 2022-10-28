<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Models\Auth\User as UserModel;
use App\Nova\Actions\Models\Auth\User\GivePermissionAction;
use App\Nova\Actions\Models\Auth\User\GiveRoleAction;
use App\Nova\Actions\Models\Auth\User\RevokePermissionAction;
use App\Nova\Actions\Models\Auth\User\RevokeRoleAction;
use App\Nova\Resources\BaseResource;
use Exception;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class User.
 */
class User extends BaseResource
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
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, UserModel::ATTRIBUTE_EMAIL);
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
        return __('nova.resources.group.auth');
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
        return __('nova.resources.label.users');
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
        return __('nova.resources.singularLabel.user');
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
            new Column(UserModel::ATTRIBUTE_NAME),
            new Column(UserModel::ATTRIBUTE_EMAIL),
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
            ID::make(__('nova.fields.base.id'), UserModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Gravatar::make()->maxWidth(50)
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.user.name'), UserModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            Email::make(__('nova.fields.user.email'), UserModel::ATTRIBUTE_EMAIL)
                ->sortable()
                ->rules(['required', 'email', 'max:192'])
                ->creationRules(Rule::unique(UserModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(UserModel::TABLE)
                        ->ignore($request->get('resourceId'), UserModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsToMany::make(__('nova.resources.label.roles'), UserModel::RELATION_ROLES, Role::class)
                ->filterable(),

            BelongsToMany::make(__('nova.resources.label.permissions'), UserModel::RELATION_PERMISSIONS, Permission::class)
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
                (new GivePermissionAction())
                    ->confirmButtonText(__('nova.actions.base.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),

                (new GiveRoleAction())
                    ->confirmButtonText(__('nova.actions.base.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),

                (new RevokePermissionAction())
                    ->confirmButtonText(__('nova.actions.base.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),

                (new RevokeRoleAction())
                    ->confirmButtonText(__('nova.actions.base.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),
            ]
        );
    }
}
