<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song\RelationManagers\SongStaffSongRelationManager;
use App\Filament\Resources\Wiki\SongStaff\Pages\ListSongStaff;
use App\Filament\Resources\Wiki\SongStaff\Pages\ViewSongStaff;
use App\Filament\Resources\Wiki\Theme\Pages\ViewTheme;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SongStaffResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SongStaff::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.song_staff');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.song_staff');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedMusicalNote;
    }

    public static function getRecordTitleAttribute(): string
    {
        return SongStaff::ATTRIBUTE_ID;
    }

    public static function getRecordSlug(): string
    {
        return 'song-staff';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            SongStaff::RELATION_ARTIST,
            SongStaff::RELATION_MEMBER,
            SongStaff::RELATION_SONG,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                BelongsTo::make(SongStaff::ATTRIBUTE_SONG)
                    ->resource(SongResource::class)
                    ->required()
                    ->hiddenOn([SongStaffSongRelationManager::class])
                    ->columnSpanFull(),

                TextInput::make(SongStaff::ATTRIBUTE_ROLE)
                    ->label(__('filament.fields.song_staff.role.name'))
                    ->helperText(__('filament.fields.song_staff.role.help'))
                    ->required()
                    ->datalist(SongStaff::$roles)
                    ->columnSpanFull(),

                BelongsTo::make(SongStaff::ATTRIBUTE_ARTIST)
                    ->resource(ArtistResource::class)
                    ->showCreateOption()
                    ->required()
                    ->columnSpan(1),

                TextInput::make(SongStaff::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song_staff.as.name'))
                    ->helperText(__('filament.fields.song_staff.as.help'))
                    ->columnSpan(1),

                TextInput::make(SongStaff::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.song_staff.alias.name'))
                    ->helperText(__('filament.fields.song_staff.alias.help'))
                    ->columnSpan(1),

                BelongsTo::make(SongStaff::ATTRIBUTE_MEMBER)
                    ->resource(ArtistResource::class)
                    ->showCreateOption()
                    ->label(__('filament.fields.song_staff.member'))
                    ->columnSpan(1),

                TextInput::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                    ->label(__('filament.fields.song_staff.member_as.name'))
                    ->helperText(__('filament.fields.song_staff.member_as.help'))
                    ->columnSpan(1),

                TextInput::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                    ->label(__('filament.fields.song_staff.member_alias.name'))
                    ->helperText(__('filament.fields.song_staff.member_alias.help'))
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SongStaff::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(SongStaff::RELATION_SONG, SongResource::class)
                    ->hiddenOn([SongStaffSongRelationManager::class])
                    ->limit(20)
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->whereRelation(SongStaff::RELATION_SONG, Song::ATTRIBUTE_TITLE, ComparisonOperator::LIKE->value, "%{$search}%"),
                        isIndividual: true
                    ),

                BelongsToColumn::make(SongStaff::RELATION_ARTIST, ArtistResource::class)
                    ->limit(20),

                BelongsToColumn::make(SongStaff::RELATION_MEMBER, ArtistResource::class)
                    ->label(__('filament.fields.song_staff.member'))
                    ->limit(20),

                TextColumn::make(SongStaff::ATTRIBUTE_ROLE)
                    ->label(__('filament.fields.song_staff.role.name'))
                    ->searchable(true, isIndividual: true),

                TextColumn::make(SongStaff::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.song_staff.alias.name'))
                    ->limit(20),

                TextColumn::make(SongStaff::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song_staff.as.name'))
                    ->limit(20),

                TextColumn::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                    ->label(__('filament.fields.song_staff.member_alias.name'))
                    ->limit(20),

                TextColumn::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                    ->label(__('filament.fields.song_staff.member_as.name'))
                    ->limit(20),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SongStaff::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(SongStaff::RELATION_SONG, SongResource::class)
                            ->hiddenOn(ViewTheme::class),

                        BelongsToEntry::make(SongStaff::RELATION_ARTIST, ArtistResource::class),

                        TextEntry::make(SongStaff::ATTRIBUTE_ROLE)
                            ->label(__('filament.fields.song_staff.role.name')),

                        TextEntry::make(SongStaff::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.song_staff.alias.name')),

                        TextEntry::make(SongStaff::ATTRIBUTE_AS)
                            ->label(__('filament.fields.song_staff.as.name')),

                        BelongsToEntry::make(SongStaff::RELATION_MEMBER, ArtistResource::class)
                            ->label(__('filament.fields.song_staff.member'))
                            ->visible(fn ($state): bool => filled($state)),

                        TextEntry::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                            ->label(__('filament.fields.song_staff.member_alias.name'))
                            ->visible(fn (Get $get): bool => filled($get(SongStaff::RELATION_MEMBER))),

                        TextEntry::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                            ->label(__('filament.fields.song_staff.member_as.name'))
                            ->visible(fn (Get $get): bool => filled($get(SongStaff::RELATION_MEMBER))),
                    ])
                    ->columns(fn ($livewire): int => $livewire instanceof ViewTheme ? 4 : 2),

                TimestampSection::make()
                    ->hiddenOn(ViewTheme::class),
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
                    TextConstraint::make(SongStaff::ATTRIBUTE_ROLE)
                        ->label(__('filament.fields.song_staff.role.name')),

                    TextConstraint::make(SongStaff::ATTRIBUTE_AS)
                        ->label(__('filament.fields.song_staff.as.name')),

                    TextConstraint::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                        ->label(__('filament.fields.song_staff.member_alias.name')),

                    TextConstraint::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                        ->label(__('filament.fields.song_staff.member_as.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
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
            'index' => ListSongStaff::route('/'),
            'view' => ViewSongStaff::route('/{record:id}'),
        ];
    }
}
