<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\List\PlaylistVisibility;
use App\Filament\Actions\Models\List\AssignHashidsAction;
use App\Filament\Actions\Models\List\Playlist\FixPlaylistAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\Pages\ListPlaylists;
use App\Filament\Resources\List\Playlist\Pages\ViewPlaylist;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Filament\Resources\List\Playlist\TrackResource;
use App\Models\List\Playlist;
use Filament\Forms\Components\Textarea;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlaylistResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Playlist::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.playlist');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.playlists');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::LIST;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedPlay;
    }

    public static function getRecordSlug(): string
    {
        return 'playlists';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Playlist::ATTRIBUTE_NAME;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            Playlist::RELATION_USER,
            Playlist::RELATION_FIRST,
            Playlist::RELATION_LAST,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(Playlist::ATTRIBUTE_USER)
                    ->resource(UserResource::class),

                TextInput::make(Playlist::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->helperText(__('filament.fields.playlist.name.help'))
                    ->required()
                    ->maxLength(192),

                Select::make(Playlist::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->helperText(__('filament.fields.playlist.visibility.help'))
                    ->options(PlaylistVisibility::class)
                    ->required(),

                TextInput::make(Playlist::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist.hashid.name'))
                    ->helperText(__('filament.fields.playlist.hashid.help'))
                    ->readOnly(),

                BelongsTo::make(Playlist::ATTRIBUTE_FIRST)
                    ->resource(TrackResource::class)
                    ->label(__('filament.fields.playlist.first.name')),

                BelongsTo::make(Playlist::ATTRIBUTE_LAST)
                    ->resource(TrackResource::class)
                    ->label(__('filament.fields.playlist.last.name')),

                Textarea::make(Playlist::ATTRIBUTE_DESCRIPTION)
                    ->label(__('filament.fields.playlist.description.name'))
                    ->helperText(__('filament.fields.playlist.description.help'))
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(Playlist::RELATION_USER, UserResource::class),

                TextColumn::make(Playlist::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Playlist::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column): mixed => $column->getState())
                    ->copyableWithMessage(),

                TextColumn::make(Playlist::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->formatStateUsing(fn (PlaylistVisibility $state): ?string => $state->localize()),

                TextColumn::make(Playlist::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist.hashid.name'))
                    ->copyableWithMessage()
                    ->searchable(isIndividual: true),

                BelongsToColumn::make(Playlist::RELATION_FIRST, TrackResource::class)
                    ->label(__('filament.fields.playlist.first.name')),

                BelongsToColumn::make(Playlist::RELATION_LAST, TrackResource::class)
                    ->label(__('filament.fields.playlist.last.name')),

                TextColumn::make(Playlist::ATTRIBUTE_DESCRIPTION)
                    ->label(__('filament.fields.playlist.description.name'))
                    ->hidden(),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(Playlist::RELATION_USER, UserResource::class),

                        TextEntry::make(Playlist::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.playlist.name.name'))
                            ->limit(30)
                            ->copyableWithMessage(),

                        TextEntry::make(Playlist::ATTRIBUTE_VISIBILITY)
                            ->label(__('filament.fields.playlist.visibility.name'))
                            ->formatStateUsing(fn (PlaylistVisibility $state): ?string => $state->localize()),

                        TextEntry::make(Playlist::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist.hashid.name'))
                            ->copyableWithMessage(),

                        BelongsToEntry::make(Playlist::RELATION_FIRST, TrackResource::class)
                            ->label(__('filament.fields.playlist.first.name')),

                        BelongsToEntry::make(Playlist::RELATION_LAST, TrackResource::class)
                            ->label(__('filament.fields.playlist.last.name')),

                        TextEntry::make(Playlist::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Playlist::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.fields.playlist.description.name'))
                            ->markdown()
                            ->copyableWithMessage()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    TextConstraint::make(Playlist::ATTRIBUTE_NAME)
                        ->label(__('filament.fields.playlist.name.name')),

                    SelectConstraint::make(Playlist::ATTRIBUTE_VISIBILITY)
                        ->label(__('filament.fields.playlist.visibility.name'))
                        ->options(PlaylistVisibility::class)
                        ->multiple(),

                    TextConstraint::make(Playlist::ATTRIBUTE_HASHID)
                        ->label(__('filament.fields.playlist.hashid.name')),

                    TextConstraint::make(Playlist::ATTRIBUTE_DESCRIPTION)
                        ->label(__('filament.fields.playlist.description.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            AssignHashidsAction::make()
                ->setConnection('playlists'),

            FixPlaylistAction::make(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                ImageRelationManager::class,
                TrackPlaylistRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPlaylists::route('/'),
            'view' => ViewPlaylist::route('/{record:playlist_id}'),
        ];
    }
}
