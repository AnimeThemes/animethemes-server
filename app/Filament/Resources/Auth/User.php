<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Auth\User\GivePermissionAction;
use App\Filament\Actions\Models\Auth\User\GiveRoleAction;
use App\Filament\Actions\Models\Auth\User\RevokePermissionAction;
use App\Filament\Actions\Models\Auth\User\RevokeRoleAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User\Pages\ListUsers;
use App\Filament\Resources\Auth\User\Pages\ViewUser;
use App\Filament\Resources\Auth\User\RelationManagers\PermissionUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\PlaylistUserRelationManager;
use App\Filament\Resources\Auth\User\RelationManagers\RoleUserRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User as UserModel;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class User extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = UserModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.users');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::AUTH;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedUsers;
    }

    public static function getRecordSlug(): string
    {
        return 'users';
    }

    public static function getRecordTitleAttribute(): string
    {
        return UserModel::ATTRIBUTE_NAME;
    }

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

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(UserModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                ImageColumn::make('avatar')
                    ->label(__('filament.fields.user.avatar'))
                    ->defaultImageUrl(fn (UserModel $model): string => $model->getFilamentAvatarUrl())
                    ->circular(),

                TextColumn::make(UserModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.user.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(UserModel::ATTRIBUTE_EMAIL)
                    ->label(__('filament.fields.user.email'))
                    ->icon(Heroicon::Envelope)
                    ->searchable(isIndividual: true),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        ImageEntry::make('avatar')
                            ->label(__('filament.fields.user.avatar'))
                            ->defaultImageUrl(fn (UserModel $model): string => $model->getFilamentAvatarUrl())
                            ->circular(),

                        TextEntry::make(UserModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.user.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(UserModel::ATTRIBUTE_EMAIL)
                            ->label(__('filament.fields.user.email'))
                            ->icon(Heroicon::Envelope),

                        TextEntry::make(UserModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
                RoleUserRelationManager::class,
                PermissionUserRelationManager::class,
                PlaylistUserRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            GiveRoleAction::make(),

            RevokeRoleAction::make(),

            GivePermissionAction::make(),

            RevokePermissionAction::make(),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record:id}'),
        ];
    }
}
