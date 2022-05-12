<?php

declare(strict_types=1);

namespace App\Nova\Actions\Auth\User;

use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class RevokePermissionAction.
 */
class RevokePermissionAction extends Action
{
    final public const FIELD_PERMISSION = 'permission';

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.revoke_permission');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, User>  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $permission = Permission::findById(intval($fields->get(self::FIELD_PERMISSION)));

        foreach ($models as $user) {
            $user->revokePermissionTo($permission);
        }

        return $models;
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $permissions = Permission::all([Permission::ATTRIBUTE_ID, Permission::ATTRIBUTE_NAME])
            ->keyBy(Permission::ATTRIBUTE_ID)
            ->map(fn (Permission $permission) => $permission->name)
            ->toArray();

        return [
            Select::make(__('nova.permission'), self::FIELD_PERMISSION)
                ->searchable()
                ->required()
                ->options($permissions)
                ->rules('required'),
        ];
    }
}
