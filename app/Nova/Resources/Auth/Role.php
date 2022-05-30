<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Models\Auth\Role as RoleModel;
use App\Nova\Actions\Auth\Role\GivePermissionAction;
use App\Nova\Actions\Auth\Role\RevokePermissionAction;
use App\Nova\Resources\Resource;
use Exception;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Role.
 */
class Role extends Resource
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
        return __('nova.roles');
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
        return __('nova.role');
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
            ID::make(__('nova.id'), RoleModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), RoleModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->showOnPreview()
                ->filterable(),

            BelongsToMany::make(__('nova.permissions'), RoleModel::RELATION_PERMISSIONS, Permission::class)
                ->filterable(),

            BelongsToMany::make(__('nova.users'), RoleModel::RELATION_USERS, User::class)
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
                    ->confirmButtonText(__('nova.confirm'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),

                (new RevokePermissionAction())
                    ->confirmButtonText(__('nova.confirm'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline(),
            ]
        );
    }
}
