<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\Role\GivePermissionAction;
use App\Filament\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Auth\Role\Pages\ListRoles;
use App\Filament\Resources\Auth\Role\Pages\ViewRole;
use App\Filament\Resources\Auth\Role\RelationManagers\PermissionRoleRelationManager;
use App\Filament\Resources\Auth\Role\RelationManagers\UserRoleRelationManager;
use App\Models\Auth\Role as RoleModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.resources.roles');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'roles';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return RoleModel::ATTRIBUTE_NAME;
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
                    ->rules(['nullable', 'integer', 'min:1']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(RoleModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(RoleModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                IconColumn::make(RoleModel::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->boolean(),

                ColorColumn::make(RoleModel::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name')),

                TextColumn::make(RoleModel::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name')),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(RoleModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(RoleModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.role.name'))
                            ->copyableWithMessage(),

                        IconEntry::make(RoleModel::ATTRIBUTE_DEFAULT)
                            ->label(__('filament.fields.role.default.name'))
                            ->boolean(),

                        ColorEntry::make(RoleModel::ATTRIBUTE_COLOR)
                            ->label(__('filament.fields.role.color.name')),

                        TextEntry::make(RoleModel::ATTRIBUTE_PRIORITY)
                            ->label(__('filament.fields.role.priority.name')),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
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
        return [
            CheckboxFilter::make(RoleModel::ATTRIBUTE_DEFAULT)
                ->label(__('filament.fields.role.default.name')),

            NumberFilter::make(RoleModel::ATTRIBUTE_PRIORITY)
                ->label(__('filament.fields.role.priority.name')),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),

            ActionGroup::make([
                GivePermissionAction::make('give-permission'),

                RevokePermissionAction::make('revoke-permission'),
            ]),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),
        ];
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
            'view' => ViewRole::route('/{record:id}'),
        ];
    }
}
