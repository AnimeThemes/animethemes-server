<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Filament\Actions\Models\Auth\User\GivePermissionAction;
use App\Filament\Actions\Models\Auth\User\GiveRoleAction;
use App\Filament\Actions\Models\Auth\User\RevokePermissionAction;
use App\Filament\Actions\Models\Auth\User\RevokeRoleAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Auth\User\Pages\CreateUser;
use App\Filament\Resources\Auth\User\Pages\EditUser;
use App\Filament\Resources\Auth\User\Pages\ListUsers;
use App\Filament\Resources\Auth\User\Pages\ViewUser;
use App\Filament\Resources\Auth\User\RelationManagers\PermissionUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\PlaylistUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\RoleUserRelationManager;
use App\Models\Auth\User as UserModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

/**
 * Class User.
 */
class User extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = UserModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.user');
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
        return __('filament.resources.label.users');
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
        return __('filament.resources.icon.users');
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
        return 'users';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        return UserModel::ATTRIBUTE_ID;
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
                TextInput::make(UserModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.user.name'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                TextInput::make(UserModel::ATTRIBUTE_EMAIL)
                    ->label(__('filament.fields.user.email'))
                    ->email()
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'email', 'max:192']),
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
                TextColumn::make(UserModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                ImageColumn::make('avatar')
                    ->label(__('filament.fields.user.avatar'))
                    ->defaultImageUrl(fn (UserModel $model) => $model->getFilamentAvatarUrl())
                    ->circular(),

                TextColumn::make(UserModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.user.name'))
                    ->sortable()
                    ->searchable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(UserModel::ATTRIBUTE_EMAIL)
                    ->label(__('filament.fields.user.email'))
                    ->icon('heroicon-m-envelope')
                    ->toggleable(),
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
                        ImageEntry::make('avatar')
                            ->label(__('filament.fields.user.avatar'))
                            ->defaultImageUrl(fn (UserModel $model) => $model->getFilamentAvatarUrl())
                            ->circular(),

                        TextEntry::make(UserModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.user.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(UserModel::ATTRIBUTE_EMAIL)
                            ->label(__('filament.fields.user.email'))
                            ->icon('heroicon-m-envelope'),

                        TextEntry::make(UserModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        RoleUserRelationManager::class,
                        PermissionUserRelationManager::class,
                        PlaylistUserRelationManager::class,
                    ],
                    parent::getBaseRelations(),
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

                    GivePermissionAction::make('give-permission'),

                    RevokePermissionAction::make('revoke-permission'),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record:id}'),
            'edit' => EditUser::route('/{record:id}/edit'),
        ];
    }
}
