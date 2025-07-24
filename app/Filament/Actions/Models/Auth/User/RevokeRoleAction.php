<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class RevokeRoleAction extends BaseAction
{
    final public const FIELD_ROLE = 'role';

    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'user-revoke-role';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.user.revoke_role.name'));

        $this->action(fn (User $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): void
    {
        $role = Role::findById(intval(Arr::get($data, self::FIELD_ROLE)));

        $user->removeRole($role);
    }

    /**
     * Get the schema available on the action.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        $roles = Role::query()
            ->whereHas(Role::RELATION_USERS, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
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
