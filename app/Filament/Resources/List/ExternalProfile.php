<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Filament\Actions\Models\List\External\SyncExternalProfileAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\Pages\ListExternalProfiles;
use App\Filament\Resources\List\External\Pages\ViewExternalProfile;
use App\Filament\Resources\List\External\RelationManagers\ExternalEntryExternalProfileRelationManager;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExternalProfile extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalProfileModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.external_profile');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.external_profiles');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::LIST;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedUser;
    }

    public static function getRecordSlug(): string
    {
        return 'external-profiles';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ExternalProfileModel::ATTRIBUTE_NAME;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([ExternalProfileModel::RELATION_USER]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(ExternalProfileModel::ATTRIBUTE_USER)
                    ->resource(User::class),

                TextInput::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name'))
                    ->helperText(__('filament.fields.external_profile.name.help'))
                    ->required()
                    ->maxLength(192),

                Select::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->helperText(__('filament.fields.external_profile.site.help'))
                    ->options(ExternalProfileSite::class)
                    ->required(),

                Select::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->helperText(__('filament.fields.external_profile.visibility.help'))
                    ->options(ExternalProfileVisibility::class)
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(ExternalProfileModel::RELATION_USER, User::class),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name')),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->formatStateUsing(fn (ExternalProfileSite $state): ?string => $state->localize()),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->formatStateUsing(fn (ExternalProfileVisibility $state): ?string => $state->localize()),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(ExternalProfileModel::RELATION_USER, User::class, true),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.external_profile.name.name')),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_SITE)
                            ->label(__('filament.fields.external_profile.site.name'))
                            ->formatStateUsing(fn (ExternalProfileSite $state): ?string => $state->localize()),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                            ->label(__('filament.fields.external_profile.visibility.name'))
                            ->formatStateUsing(fn (ExternalProfileVisibility $state): ?string => $state->localize()),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_ID)
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
                ExternalEntryExternalProfileRelationManager::class,

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
            SyncExternalProfileAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListExternalProfiles::route('/'),
            'view' => ViewExternalProfile::route('/{record:profile_id}'),
        ];
    }
}
