<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\Permission\GiveRoleAction;
use App\Filament\Actions\Models\Auth\Permission\RevokeRoleAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Auth\Permission\Pages\CreatePermission;
use App\Filament\Resources\Auth\Permission\Pages\EditPermission;
use App\Filament\Resources\Auth\Permission\Pages\ListPermissions;
use App\Filament\Resources\Auth\Permission\Pages\ViewPermission;
use App\Filament\Resources\Auth\Permission\RelationManagers\RolePermissionRelationManager;
use App\Filament\Resources\Auth\Permission\RelationManagers\UserPermissionRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Permission as PermissionModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

/**
 * Class Permission.
 */
class Permission extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = PermissionModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.permission');
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
        return __('filament.resources.label.permissions');
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
        return __('filament.resources.icon.permissions');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'permissions';
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
        return PermissionModel::ATTRIBUTE_NAME;
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
                TextInput::make(PermissionModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.permission.name'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->hiddenOn(['edit']),
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
            ->recordUrl(fn (PermissionModel $record): string => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make(PermissionModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(PermissionModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.permission.name'))
                    ->sortable()
                    ->searchable()
                    ->copyableWithMessage(),
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
                        TextEntry::make(PermissionModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PermissionModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.permission.name'))
                            ->copyableWithMessage(),
                    ]),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ])
            ->columns(2);
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        RolePermissionRelationManager::class,
                        UserPermissionRelationManager::class,
                    ],
                )
            ),
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
                    GiveRoleAction::make('give-role'),

                    RevokeRoleAction::make('revoke-role'),
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
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record:id}'),
            'edit' => EditPermission::route('/{record:id}/edit'),
        ];
    }
}
