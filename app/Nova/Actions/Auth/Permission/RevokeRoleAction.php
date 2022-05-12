<?php

declare(strict_types=1);

namespace App\Nova\Actions\Auth\Permission;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class RevokeRoleAction.
 */
class RevokeRoleAction extends Action
{
    final public const FIELD_ROLE = 'role';

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.revoke_role');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Permission>  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $role = Role::findById(intval($fields->get(self::FIELD_ROLE)));

        foreach ($models as $permission) {
            $permission->removeRole($role);
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
        $roles = Role::all([Role::ATTRIBUTE_ID, Role::ATTRIBUTE_NAME])
            ->keyBy(Role::ATTRIBUTE_ID)
            ->map(fn (Role $role) => $role->name)
            ->toArray();

        return [
            Select::make(__('nova.role'), self::FIELD_ROLE)
                ->searchable()
                ->required()
                ->options($roles)
                ->rules('required'),
        ];
    }
}
