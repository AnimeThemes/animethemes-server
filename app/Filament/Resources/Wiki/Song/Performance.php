<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song;

use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Song;
use App\Filament\Resources\Wiki\Song\Performance\Pages\EditPerformance;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ListPerformances;
use App\Filament\Resources\Wiki\Song\Performance\Pages\ViewPerformance;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song as SongModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Performance.
 */
class Performance extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = PerformanceModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.performance');
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
        return __('filament.resources.label.performances');
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
        return __('filament.resources.group.wiki');
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
        return __('filament-icons.resources.performances');
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
        return PerformanceModel::ATTRIBUTE_ID;
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
        return 'performances';
    }

    /**
     * Get the eloquent query for the resource.
     *
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
                    Membership::class => [Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]
                ]);
            },
            PerformanceModel::RELATION_SONG,
        ]);
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
                BelongsTo::make(PerformanceModel::ATTRIBUTE_SONG)
                    ->resource(Song::class)
                    ->required()
                    ->hiddenOn([PerformanceSongRelationManager::class])
                    ->disabledOn('edit')
                    ->columnSpanFull(),

                ...static::performancesFields(),
            ])
            ->columns(2);
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
                TextColumn::make(PerformanceModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(PerformanceModel::RELATION_SONG, Song::class)
                    ->hiddenOn([PerformanceSongRelationManager::class]),

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
                            return $record->artist->artist->name;
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
            ])
            ->searchable();
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
                        TextEntry::make(PerformanceModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(PerformanceModel::RELATION_SONG, Song::class),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST, ArtistResource::class)
                            ->hidden(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST . '.' . Membership::RELATION_ARTIST, ArtistResource::class)
                            ->label(__('filament.fields.performance.artist'))
                            ->hidden(fn ($state) => is_null($state)),

                        BelongsToEntry::make(PerformanceModel::RELATION_ARTIST . '.' . Membership::RELATION_MEMBER, ArtistResource::class, true)
                            ->label(__('filament.fields.membership.member'))
                            ->hidden(fn ($state) => is_null($state)),

                        TextEntry::make(PerformanceModel::RELATION_ARTIST . '.' . Membership::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.membership.alias.name'))
                            ->visible(fn (PerformanceModel $record) => $record->artist instanceof Membership),

                        TextEntry::make(PerformanceModel::RELATION_ARTIST . '.' . Membership::ATTRIBUTE_AS)
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
                    [],
                    parent::getBaseRelations(),
                )
            ),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return array_merge(
            [],
            parent::getFilters(),
        );
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
            [],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
            [],
        );
    }

    /**
     * Get the performance fields to create a performance.
     *
     * @return array
     */
    public static function performancesFields(): array
    {
        return [
            Repeater::make(SongModel::RELATION_PERFORMANCES)
                ->label(__('filament.resources.label.artists'))
                ->addActionLabel(__('filament.buttons.add').' '.__('filament.resources.singularLabel.artist'))
                ->hiddenOn([PerformanceArtistRelationManager::class, GroupPerformanceArtistRelationManager::class])
                ->live(true)
                ->key('song.performances')
                ->collapsible()
                ->defaultItems(0)
                ->columns(3)
                ->columnSpanFull()
                ->formatStateUsing(function ($livewire, Get $get, $state) {
                    if ($livewire instanceof PerformanceSongRelationManager) {
                        $song = $livewire->getOwnerRecord();
                    } else {
                        if ($songId = $get(PerformanceModel::ATTRIBUTE_SONG)) {
                            $song = SongModel::find($songId);
                        } else return $state;
                    }

                    if (!($song instanceof SongModel)) {
                        return $state;
                    }
                    return PerformanceSongRelationManager::formatArtists($song);
                })
                ->schema([
                    BelongsTo::make(Artist::ATTRIBUTE_ID)
                        ->resource(ArtistResource::class)
                        ->showCreateOption()
                        ->required()
                        ->rules(['required']),

                    TextInput::make(PerformanceModel::ATTRIBUTE_AS)
                        ->label(__('filament.fields.performance.as.name'))
                        ->helperText(__('filament.fields.performance.as.help')),

                    TextInput::make(PerformanceModel::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.performance.alias.name'))
                        ->helperText(__('filament.fields.performance.alias.help')),

                    Repeater::make('memberships')
                        ->label(__('filament.resources.label.memberships'))
                        ->collapsible()
                        ->defaultItems(0)
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            BelongsTo::make(Membership::ATTRIBUTE_MEMBER)
                                ->resource(ArtistResource::class)
                                ->showCreateOption()
                                ->required()
                                ->rules(['required']),

                            TextInput::make(Membership::ATTRIBUTE_AS)
                                ->label(__('filament.fields.membership.as.name'))
                                ->helperText(__('filament.fields.membership.as.help')),

                            TextInput::make(Membership::ATTRIBUTE_ALIAS)
                                ->label(__('filament.fields.membership.alias.name'))
                                ->helperText(__('filament.fields.membership.alias.help')),
                        ]),
                ])
                ->saveRelationshipsUsing(function (Get $get, ?array $state) {
                    $song = SongModel::find($get(PerformanceModel::ATTRIBUTE_SONG));
                    PerformanceSongRelationManager::saveArtists($song, $state);
                })
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
            'index' => ListPerformances::route('/'),
            'edit' => EditPerformance::route('/{record:performance_id}/edit'),
            'view' => ViewPerformance::route('/{record:performance_id}'),
        ];
    }
}
