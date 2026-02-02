<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Auth\Role\GivePermissionAction;
use App\Filament\Actions\Models\Auth\Role\RevokePermissionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\Role\Pages\ListRoles;
use App\Filament\Resources\Auth\Role\Pages\ViewRole;
use App\Filament\Resources\Auth\Role\RelationManagers\PermissionRoleRelationManager;
use App\Filament\Resources\Auth\Role\RelationManagers\UserRoleRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Role;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Role::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.roles');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::AUTH;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedBriefcase;
    }

    public static function getRecordSlug(): string
    {
        return 'roles';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Role::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Role::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->required()
                    ->maxLength(192),

                Checkbox::make(Role::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->helperText(__('filament.fields.role.default.help')),

                ColorPicker::make(Role::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name'))
                    ->helperText(__('filament.fields.role.color.help')),

                TextInput::make(Role::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name'))
                    ->helperText(__('filament.fields.role.priority.help'))
                    ->integer()
                    ->minValue(1),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultSort(Role::ATTRIBUTE_PRIORITY, 'desc')
            ->columns([
                TextColumn::make(Role::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Role::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.role.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                IconColumn::make(Role::ATTRIBUTE_DEFAULT)
                    ->label(__('filament.fields.role.default.name'))
                    ->boolean(),

                ColorColumn::make(Role::ATTRIBUTE_COLOR)
                    ->label(__('filament.fields.role.color.name')),

                TextColumn::make(Role::ATTRIBUTE_PRIORITY)
                    ->label(__('filament.fields.role.priority.name')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Role::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Role::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.role.name'))
                            ->copyableWithMessage(),

                        IconEntry::make(Role::ATTRIBUTE_DEFAULT)
                            ->label(__('filament.fields.role.default.name'))
                            ->boolean(),

                        ColorEntry::make(Role::ATTRIBUTE_COLOR)
                            ->label(__('filament.fields.role.color.name')),

                        TextEntry::make(Role::ATTRIBUTE_PRIORITY)
                            ->label(__('filament.fields.role.priority.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                PermissionRoleRelationManager::class,
                UserRoleRelationManager::class,
            ]),
        ];
    }

    /**
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            CheckboxFilter::make(Role::ATTRIBUTE_DEFAULT)
                ->label(__('filament.fields.role.default.name')),

            NumberFilter::make(Role::ATTRIBUTE_PRIORITY)
                ->label(__('filament.fields.role.priority.name')),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            GivePermissionAction::make(),

            RevokePermissionAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'view' => ViewRole::route('/{record:id}'),
        ];
    }
}
