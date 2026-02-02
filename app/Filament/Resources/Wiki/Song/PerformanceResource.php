<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ListPerformances;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ViewPerformance;
use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Filament\Resources\Wiki\SongResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PerformanceResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Performance::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.performance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.performances');
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
        return Performance::ATTRIBUTE_ID;
    }

    public static function getRecordSlug(): string
    {
        return 'performances';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo): void {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_GROUP, Membership::RELATION_MEMBER],
                ]);
            },
            Performance::RELATION_SONG,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return PerformanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Performance::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(Performance::RELATION_SONG, SongResource::class)
                    ->hiddenOn([PerformanceSongRelationManager::class])
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->whereRelation(Performance::RELATION_SONG, Song::ATTRIBUTE_TITLE, ComparisonOperator::LIKE->value, "%{$search}%"),
                        isIndividual: true
                    ),

                BelongsToColumn::make(Performance::RELATION_MEMBER, ArtistResource::class, true)
                    ->label(__('filament.fields.membership.member'))
                    ->hiddenOn([PerformanceArtistRelationManager::class, GroupPerformanceArtistRelationManager::class]),

                TextColumn::make(Performance::RELATION_ARTIST)
                    ->label(__('filament.fields.performance.artist'))
                    ->hiddenOn([PerformanceArtistRelationManager::class])
                    ->color('related-link')
                    ->weight(FontWeight::SemiBold)
                    ->state(function (Performance $record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->group->name;
                        }

                        return $record->artist->name;
                    })
                    ->url(function (Performance $record): string {
                        $related = $record->artist instanceof Membership
                            ? $record->artist->group
                            : $record->artist;

                        return ArtistResource::getUrl('view', ['record' => $related]);
                    }),

                TextColumn::make(Performance::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.performance.alias.name'))
                    ->hiddenOn(GroupPerformanceArtistRelationManager::class)
                    ->state(function (Performance $record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->alias;
                        }

                        return $record->alias;
                    }),

                TextColumn::make(Performance::ATTRIBUTE_AS)
                    ->label(__('filament.fields.performance.as.name'))
                    ->hiddenOn(GroupPerformanceArtistRelationManager::class)
                    ->state(function (Performance $record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->as;
                        }

                        return $record->as;
                    }),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Performance::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(Performance::RELATION_SONG, SongResource::class),

                        BelongsToEntry::make(Performance::RELATION_ARTIST, ArtistResource::class)
                            ->hidden(fn (Performance $record): bool => $record->artist instanceof Membership),

                        BelongsToEntry::make(Performance::RELATION_GROUP, ArtistResource::class)
                            ->label(__('filament.fields.performance.artist'))
                            ->hidden(fn ($state): bool => is_null($state)),

                        BelongsToEntry::make(Performance::RELATION_MEMBER, ArtistResource::class, true)
                            ->label(__('filament.fields.membership.member'))
                            ->hidden(fn ($state): bool => is_null($state)),

                        TextEntry::make(Performance::RELATION_ARTIST.'.'.Membership::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.membership.alias.name'))
                            ->visible(fn (Performance $record): bool => $record->artist instanceof Membership),

                        TextEntry::make(Performance::RELATION_ARTIST.'.'.Membership::ATTRIBUTE_AS)
                            ->label(__('filament.fields.membership.as.name'))
                            ->visible(fn (Performance $record): bool => $record->artist instanceof Membership),

                        TextEntry::make(Performance::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.performance.alias.name'))
                            ->hidden(fn (Performance $record): bool => $record->artist instanceof Membership),

                        TextEntry::make(Performance::ATTRIBUTE_AS)
                            ->label(__('filament.fields.performance.as.name'))
                            ->hidden(fn (Performance $record): bool => $record->artist instanceof Membership),
                    ])
                    ->columns(fn ($livewire): int => $livewire instanceof ViewTheme ? 3 : 2),

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
                    TextConstraint::make(Performance::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.performance.alias.name')),

                    TextConstraint::make(Performance::ATTRIBUTE_AS)
                        ->label(__('filament.fields.performance.as.name')),

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
            'index' => ListPerformances::route('/'),
            'view' => ViewPerformance::route('/{record:performance_id}'),
        ];
    }
}
