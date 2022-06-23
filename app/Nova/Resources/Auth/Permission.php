<?php

declare(strict_types=1);

namespace App\Nova\Resources\Auth;

use App\Models\Auth\Permission as PermissionModel;
use App\Nova\Actions\Auth\Permission\GiveRoleAction;
use App\Nova\Actions\Auth\Permission\RevokeRoleAction;
use App\Nova\Resources\BaseResource;
use Exception;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Permission.
 */
class Permission extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = PermissionModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = PermissionModel::ATTRIBUTE_NAME;

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
        return __('nova.permissions');
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
        return __('nova.permission');
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
            new Column(PermissionModel::ATTRIBUTE_NAME),
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
            ID::make(__('nova.id'), PermissionModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), PermissionModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->showOnPreview()
                ->filterable(),

            BelongsToMany::make(__('nova.roles'), PermissionModel::RELATION_ROLES, Role::class)
                ->filterable(),

            BelongsToMany::make(__('nova.users'), PermissionModel::RELATION_USERS, User::class)
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
                (new GiveRoleAction())
                    ->confirmButtonText(__('nova.confirm'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canRun(fn (NovaRequest $novaRequest) => $novaRequest->user()->can('view permission')),

                (new RevokeRoleAction())
                    ->confirmButtonText(__('nova.confirm'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canRun(fn (NovaRequest $novaRequest) => $novaRequest->user()->can('view permission')),
            ]
        );
    }
}
