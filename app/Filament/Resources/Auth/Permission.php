<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\Permission\GiveRoleAction;
use App\Filament\Actions\Models\Auth\Permission\RevokeRoleAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\Permission\Pages\ListPermissions;
use App\Filament\Resources\Auth\Permission\Pages\ViewPermission;
use App\Filament\Resources\Auth\Permission\RelationManagers\RolePermissionRelationManager;
use App\Filament\Resources\Auth\Permission\RelationManagers\UserPermissionRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Permission as PermissionModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Permission extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = PermissionModel::class;

    /**
     * Get the displayable singular label of the resource.
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.permission');
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.permissions');
    }

    /**
     * The logical group associated with the resource.
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.auth');
    }

    /**
     * The icon displayed to the resource.
     */
    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedInformationCircle;
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'permissions';
    }

    /**
     * Get the title attribute for the resource.
     */
    public static function getRecordTitleAttribute(): string
    {
        return PermissionModel::ATTRIBUTE_NAME;
    }

    /**
     * The form to the actions.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(PermissionModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.permission.name'))
                    ->required()
                    ->maxLength(192)
                    ->hiddenOn(['edit']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(fn (PermissionModel $record): string => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make(PermissionModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(PermissionModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.permission.name'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(PermissionModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PermissionModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.permission.name'))
                            ->copyableWithMessage(),
                    ]),

                TimestampSection::make(),
            ])
            ->columns(2);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                RolePermissionRelationManager::class,
                UserPermissionRelationManager::class,
            ]),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            GiveRoleAction::make(),

            RevokeRoleAction::make(),
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'view' => ViewPermission::route('/{record:id}'),
        ];
    }
}
