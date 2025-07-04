<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\Role\GivePermissionAction;
use App\Filament\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\Role\Pages\ListRoles;
use App\Filament\Resources\Auth\Role\Pages\ViewRole;
use App\Filament\Resources\Auth\Role\RelationManagers\PermissionRoleRelationManager;
use App\Filament\Resources\Auth\Role\RelationManagers\UserRoleRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Role as RoleModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role.
 */
class Role extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(RoleModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->required()
                    ->maxLength(192),

                Checkbox::make(RoleModel::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->helperText(__('filament.fields.role.default.help'))
                    ->rules(['boolean']),

                ColorPicker::make(RoleModel::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name'))
                    ->helperText(__('filament.fields.role.color.help')),

                TextInput::make(RoleModel::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name'))
                    ->helperText(__('filament.fields.role.priority.help'))
                    ->integer()
                    ->minValue(1),
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
            ->defaultSort(RoleModel::ATTRIBUTE_PRIORITY, 'desc')
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
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

                TimestampSection::make(),
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
    public static function getRecordActions(): array
    {
        return [
            GivePermissionAction::make(),

            RevokePermissionAction::make(),
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
