<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class GivePermissionAction extends BaseAction
{
    final public const FIELD_PERMISSION = 'permission';

    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'user-give-permission';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.user.give_permission.name'));

        $this->action(fn (User $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): void
    {
        $permission = Permission::findById(intval(Arr::get($data, self::FIELD_PERMISSION)));

        $user->givePermissionTo($permission);
    }

    /**
     * Get the schema available on the action.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        $permissions = Permission::query()
            ->whereDoesntHave(Permission::RELATION_USERS, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
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
