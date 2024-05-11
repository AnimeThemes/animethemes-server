<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Auth\Role;

use App\Filament\Components\Fields\Select;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Support\Arr;

/**
 * Class RevokePermissionHeaderAction.
 */
class RevokePermissionHeaderAction extends Action
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

        $this->action(fn (Role $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  Role  $role
     * @param  array  $data
     * @return void
     */
    public function handle(Role $role, array $data): void
    {
        $permission = Permission::findById(intval(Arr::get($data, self::FIELD_PERMISSION)));

        $role->revokePermissionTo($permission);
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
