<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Auth\User;

use App\Filament\Components\Fields\Select;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Forms\Form;
use Illuminate\Support\Arr;

/**
 * Trait RevokeRoleActionTrait.
 */
trait RevokeRoleActionTrait
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

        $this->label(__('filament.actions.user.revoke_role.name'));

        $this->action(fn (User $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  User  $user
     * @param  array  $data
     * @return void
     */
    public function handle(User $user, array $data): void
    {
        $role = Role::findById(intval(Arr::get($data, self::FIELD_ROLE)));

        $user->removeRole($role);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        $roles = Role::all([Role::ATTRIBUTE_ID, Role::ATTRIBUTE_NAME])
            ->keyBy(Role::ATTRIBUTE_ID)
            ->map(fn (Role $role) => $role->name)
            ->toArray();

        return $form
            ->schema([
                Select::make(self::FIELD_ROLE)
                    ->label(__('filament.resources.singularLabel.role'))
                    ->searchable()
                    ->required()
                    ->options($roles),
            ]);
    }
}
