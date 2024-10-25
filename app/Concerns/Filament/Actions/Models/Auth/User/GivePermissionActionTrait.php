<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Auth\User;

use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Filament\Forms\Form;
use Illuminate\Support\Arr;

/**
 * Trait GivePermissionActionTrait.
 */
trait GivePermissionActionTrait
{
    final public const FIELD_PERMISSION = 'permission';

    /**
     * Initial setup for the action.
     *
     * @return void
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
     * @param  User  $user
     * @param  array  $data
     * @return void
     */
    public function handle(User $user, array $data): void
    {
        $permission = Permission::findById(intval(Arr::get($data, self::FIELD_PERMISSION)));

        $user->givePermissionTo($permission);
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
        $permissions = Permission::all([Permission::ATTRIBUTE_ID, Permission::ATTRIBUTE_NAME])
            ->keyBy(Permission::ATTRIBUTE_ID)
            ->map(fn (Permission $permission) => $permission->name)
            ->toArray();

        return $form
            ->schema([
                Select::make(self::FIELD_PERMISSION)
                    ->label(__('filament.resources.singularLabel.permission'))
                    ->searchable()
                    ->required()
                    ->options($permissions)
                    ->rules('required'),
            ]);
    }
}
