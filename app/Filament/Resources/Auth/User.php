<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use App\Filament\Actions\Models\Auth\User\GivePermissionAction;
use App\Filament\Actions\Models\Auth\User\GiveRoleAction;
use App\Filament\Actions\Models\Auth\User\RevokePermissionAction;
use App\Filament\Actions\Models\Auth\User\RevokeRoleAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User\Pages\ListUsers;
use App\Filament\Resources\Auth\User\Pages\ViewUser;
use App\Filament\Resources\Auth\User\RelationManagers\PermissionUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\PlaylistUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\RoleUserRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User as UserModel;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class User extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
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
        return __('filament-icons.resources.users');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'users';
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
        return UserModel::ATTRIBUTE_NAME;
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
                TextInput::make(UserModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.user.name'))
                    ->required()
                    ->maxLength(192),

                TextInput::make(UserModel::ATTRIBUTE_EMAIL)
                    ->label(__('filament.fields.user.email'))
                    ->email()
                    ->required()
                    ->maxLength(192),
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
                TextColumn::make(UserModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                ImageColumn::make('avatar')
                    ->label(__('filament.fields.user.avatar'))
                    ->defaultImageUrl(fn (UserModel $model) => $model->getFilamentAvatarUrl())
                    ->circular(),

                TextColumn::make(UserModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.user.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(UserModel::ATTRIBUTE_EMAIL)
                    ->label(__('filament.fields.user.email'))
                    ->icon(__('filament-icons.fields.user.email'))
                    ->searchable(isIndividual: true),
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
                        ImageEntry::make('avatar')
                            ->label(__('filament.fields.user.avatar'))
                            ->defaultImageUrl(fn (UserModel $model) => $model->getFilamentAvatarUrl())
                            ->circular(),

                        TextEntry::make(UserModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.user.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(UserModel::ATTRIBUTE_EMAIL)
                            ->label(__('filament.fields.user.email'))
                            ->icon(__('filament-icons.fields.user.email')),

                        TextEntry::make(UserModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
                RoleUserRelationManager::class,
                PermissionUserRelationManager::class,
                PlaylistUserRelationManager::class,

                ...parent::getBaseRelations(),
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
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),

            ActionGroup::make([
                GiveRoleAction::make('give-role'),

                RevokeRoleAction::make('revoke-role'),

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
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record:id}'),
        ];
    }
}
