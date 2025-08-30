<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Role;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class GivePermissionAction extends BaseAction
{
    final public const FIELD_PERMISSION = 'permission';

    public static function getDefaultName(): ?string
    {
        return 'role-give-permission';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.role.give_permission.name'));

        $this->action(fn (Role $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Role $role, array $data): void
    {
        $permission = Permission::findById(intval(Arr::get($data, self::FIELD_PERMISSION)));

        $role->givePermissionTo($permission);
    }

    public function getSchema(Schema $schema): Schema
    {
        $permissions = Permission::query()
            ->whereDoesntHave(Permission::RELATION_ROLES, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
            ->get([Permission::ATTRIBUTE_ID, Permission::ATTRIBUTE_NAME])
            ->keyBy(Permission::ATTRIBUTE_ID)
            ->map(fn (Permission $permission) => $permission->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_PERMISSION)
                    ->label(__('filament.resources.singularLabel.permission'))
                    ->searchable()
                    ->required()
                    ->options($permissions),
            ]);
    }
}
