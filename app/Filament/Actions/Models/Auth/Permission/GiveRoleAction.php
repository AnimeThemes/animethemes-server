<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Permission;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

/**
 * Class GiveRoleAction.
 */
class GiveRoleAction extends BaseAction
{
    final public const FIELD_ROLE = 'role';

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.permission.give_role.name'));

        $this->action(fn (Permission $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Permission  $permission
     * @param  array  $data
     * @return void
     */
    public function handle(Permission $permission, array $data): void
    {
        $role = Role::findById(intval(Arr::get($data, self::FIELD_ROLE)));

        $permission->assignRole($role);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        $roles = Role::all([Role::ATTRIBUTE_ID, Role::ATTRIBUTE_NAME])
            ->keyBy(Role::ATTRIBUTE_ID)
            ->map(fn (Role $role) => $role->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_ROLE)
                    ->label(__('filament.resources.singularLabel.role'))
                    ->searchable()
                    ->required()
                    ->options($roles),
            ]);
    }
}
