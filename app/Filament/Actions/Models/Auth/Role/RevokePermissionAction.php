<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Role;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Auth\Permission as PermissionResource;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class RevokePermissionAction extends BaseAction
{
    final public const string FIELD_PERMISSIONS = 'permissions';

    public static function getDefaultName(): ?string
    {
        return 'role-revoke-permission';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.role.revoke_permission.name'));

        $this->icon(PermissionResource::getNavigationIcon());

        $this->action(fn (Role $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Role $role, array $data): void
    {
        $permissions = Arr::get($data, self::FIELD_PERMISSIONS);

        $role->revokePermissionTo($permissions);
    }

    public function getSchema(Schema $schema): Schema
    {
        $permissions = Permission::query()
            ->whereHas(Permission::RELATION_ROLES, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
            ->get([Permission::ATTRIBUTE_ID, Permission::ATTRIBUTE_NAME])
            ->keyBy(Permission::ATTRIBUTE_ID)
            ->map(fn (Permission $permission) => $permission->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_PERMISSIONS)
                    ->label(__('filament.resources.label.permissions'))
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->options($permissions),
            ]);
    }
}
