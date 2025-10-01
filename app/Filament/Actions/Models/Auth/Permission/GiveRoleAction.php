<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Permission;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class GiveRoleAction extends BaseAction
{
    final public const string FIELD_ROLE = 'role';

    public static function getDefaultName(): ?string
    {
        return 'permission-give-role';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.permission.give_role.name'));

        $this->action(fn (Permission $record, array $data) => $this->handle($record, $data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Permission $permission, array $data): void
    {
        $role = Role::findById(intval(Arr::get($data, self::FIELD_ROLE)));

        $permission->assignRole($role);
    }

    public function getSchema(Schema $schema): Schema
    {
        $roles = Role::query()
            ->whereDoesntHave(Role::RELATION_PERMISSIONS, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
            ->get([Role::ATTRIBUTE_ID, Role::ATTRIBUTE_NAME])
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
