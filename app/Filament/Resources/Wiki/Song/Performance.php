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
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Song;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ListPerformances;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ViewPerformance;
use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song as SongModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Performance extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = PerformanceModel::class;

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
        return PerformanceModel::ATTRIBUTE_ID;
    }

    public static function getRecordSlug(): string
    {
        return 'performances';
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            PerformanceModel::RELATION_ARTIST => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_GROUP, Membership::RELATION_MEMBER],
                ]);
            },
            PerformanceModel::RELATION_SONG,
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
                TextColumn::make(PerformanceModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(PerformanceModel::RELATION_SONG, Song::class)
                    ->hiddenOn([PerformanceSongRelationManager::class])
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->whereRelation(PerformanceModel::RELATION_SONG, SongModel::ATTRIBUTE_TITLE, ComparisonOperator::LIKE->value, "%{$search}%"),
                        isIndividual: true
                    ),

                TextColumn::make('member')
                    ->label(__('filament.fields.membership.member'))
                    ->hiddenOn([PerformanceArtistRelationManager::class, GroupPerformanceArtistRelationManager::class])
                    ->state(function ($record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->member->name;
                        }

                        return null;
                    }),

                TextColumn::make(PerformanceModel::RELATION_ARTIST)
                    ->label(__('filament.fields.performance.artist'))
                    ->hiddenOn([PerformanceArtistRelationManager::class])
                    ->state(function ($record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->group->name;
                        }

                        return $record->artist->name;
                    }),

                TextColumn::make('alias')
                    ->label(__('filament.fields.membership.alias.name'))
                    ->state(function ($record) {
                        if ($record->artist instanceof Membership) {
                            return $record->artist->alias;
                        }

                        return $record->alias;
                    }),

                TextColumn::make('as')
                    ->label(__('filament.fields.membership.as.name'))
                    ->state(function ($record) {
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
                        TextEntry::make(PerformanceModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(PerformanceModel::RELATION_SONG, Song::class),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST, ArtistResource::class)
                            ->hidden(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST.'.'.Membership::RELATION_GROUP, ArtistResource::class)
                            ->label(__('filament.fields.performance.artist'))
                            ->hidden(fn ($state) => is_null($state)),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST.'.'.Membership::RELATION_MEMBER, ArtistResource::class, true)
                            ->label(__('filament.fields.membership.member'))
                            ->hidden(fn ($state) => is_null($state)),

                        TextEntry::make(PerformanceModel::RELATION_ARTIST.'.'.Membership::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.membership.alias.name'))
                            ->visible(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        TextEntry::make(PerformanceModel::RELATION_ARTIST.'.'.Membership::ATTRIBUTE_AS)
                            ->label(__('filament.fields.membership.as.name'))
                            ->visible(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        TextEntry::make(PerformanceModel::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.performance.alias.name'))
                            ->hidden(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        TextEntry::make(PerformanceModel::ATTRIBUTE_AS)
                            ->label(__('filament.fields.performance.as.name'))
                            ->hidden(fn (PerformanceModel $record) => $record->artist instanceof Membership),
                    ])
                    ->columns(2),

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
