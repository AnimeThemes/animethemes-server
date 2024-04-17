<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\Role\GivePermissionAction;
use App\Filament\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Auth\Role\Pages\CreateRole;
use App\Filament\Resources\Auth\Role\Pages\EditRole;
use App\Filament\Resources\Auth\Role\Pages\ListRoles;
use App\Filament\Resources\Auth\Role\Pages\ViewRole;
use App\Filament\Resources\Auth\Role\RelationManagers\PermissionRoleRelationManager;
use App\Filament\Resources\Auth\Role\RelationManagers\UserRoleRelationManager;
use App\Models\Auth\Role as RoleModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Role.
 */
class Role extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = RoleModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.role');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.roles');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.auth');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'roles';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return RoleModel::ATTRIBUTE_ID;
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(RoleModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                Checkbox::make(RoleModel::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->helperText(__('filament.fields.role.default.help'))
                    ->rules(['nullable', 'boolean']),

                ColorPicker::make(RoleModel::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name'))
                    ->helperText(__('filament.fields.role.color.help')),

                TextInput::make(RoleModel::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name'))
                    ->helperText(__('filament.fields.role.priority.help'))
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->rules(['nullable', 'integer', 'min:1'])
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(RoleModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(RoleModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                CheckboxColumn::make(RoleModel::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->sortable()
                    ->toggleable(),

                ColorColumn::make(RoleModel::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(RoleModel::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(RoleModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getLabel(), [
                PermissionRoleRelationManager::class,
                UserRoleRelationManager::class,
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [
                ActionGroup::make([
                    GivePermissionAction::make('give-permission')
                        ->label(__('filament.actions.role.give_permission.name'))
                        ->requiresConfirmation(),

                    RevokePermissionAction::make('revoke-permission')
                        ->label(__('filament.actions.role.revoke_permission.name'))
                        ->requiresConfirmation(),
                ]),
            ],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record:id}'),
            'edit' => EditRole::route('/{record:id}/edit'),
        ];
    }
}