<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Models\Auth\Role as RoleModel;
use App\Nova\Actions\Models\Auth\Role\GivePermissionAction;
use App\Nova\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Nova\Resources\BaseResource;
use Exception;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Role.
 */
class Role extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = RoleModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = RoleModel::ATTRIBUTE_NAME;

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
        return __('nova.resources.label.roles');
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
        return __('nova.resources.singularLabel.role');
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
            new Column(RoleModel::ATTRIBUTE_NAME),
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
            ID::make(__('nova.fields.base.id'), RoleModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.role.name'), RoleModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.role.default.name'), RoleModel::ATTRIBUTE_DEFAULT)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.role.default.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Color::make(__('nova.fields.role.color.name'), RoleModel::ATTRIBUTE_COLOR)
                ->sortable()
                ->nullable()
                ->help(__('nova.fields.role.color.help'))
                ->showOnPreview()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.role.priority.name'), RoleModel::ATTRIBUTE_PRIORITY)
                ->sortable()
                ->min(1)
                ->nullable()
                ->rules(['nullable', 'integer', 'min:1'])
                ->help(__('nova.fields.role.priority.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsToMany::make(__('nova.resources.label.permissions'), RoleModel::RELATION_PERMISSIONS, Permission::class)
                ->filterable(),

            BelongsToMany::make(__('nova.resources.label.users'), RoleModel::RELATION_USERS, User::class)
                ->filterable(),
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

                (new RevokePermissionAction())
                    ->confirmButtonText(__('nova.actions.base.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),
            ]
        );
    }
}
