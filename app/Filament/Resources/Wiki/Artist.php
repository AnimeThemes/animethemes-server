<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceAction;
use App\Filament\Actions\Models\Wiki\AttachImageAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Artist\Pages\CreateArtist;
use App\Filament\Resources\Wiki\Artist\Pages\EditArtist;
use App\Filament\Resources\Wiki\Artist\Pages\ListArtists;
use App\Filament\Resources\Wiki\Artist\Pages\ViewArtist;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\ImageArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\MemberArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\ResourceArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Models\Wiki\Artist as ArtistModel;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Class Artist.
 */
class Artist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ArtistModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.artist');
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
        return __('filament.resources.label.artists');
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
        return __('filament-icons.resources.artists');
    }

    /**
     * Determine if the resource can globally search.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function canGloballySearch(): bool
    {
        return true;
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
        return 'artists';
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
        return ArtistModel::ATTRIBUTE_NAME;
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
                TextInput::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->helperText(__('filament.fields.artist.name.help'))
                    ->required()
                    ->rules(['required', 'max:192'])
                    ->maxLength(192)
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(ArtistModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                Slug::make(ArtistModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name'))
                    ->helperText(__('filament.fields.artist.slug.help')),

                MarkdownEditor::make(ArtistModel::ATTRIBUTE_INFORMATION)
                    ->label(__('filament.fields.artist.information.name'))
                    ->helperText(__('filament.fields.artist.information.help'))
                    ->columnSpan(2)
                    ->maxLength(65535)
                    ->rules('max:65535'),
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
                TextColumn::make(ArtistModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->copyableWithMessage(),

                TextColumn::make(ArtistModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name')),

                TextColumn::make(ArtistResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.resources.as.name'))
                    ->visibleOn(ArtistResourceRelationManager::class),

                TextColumn::make(ArtistSong::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.songs.as.name'))
                    ->visibleOn([MemberArtistRelationManager::class, GroupArtistRelationManager::class]),

                TextColumn::make(ArtistSong::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.artist.songs.alias.name'))
                    ->visibleOn([MemberArtistRelationManager::class, GroupArtistRelationManager::class]),
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
                        TextEntry::make(ArtistModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ArtistModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.artist.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(ArtistModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.artist.slug.name')),

                        TextEntry::make(ArtistModel::ATTRIBUTE_INFORMATION)
                            ->label(__('filament.fields.artist.information.name'))
                            ->markdown()
                            ->columnSpanFull(),

                        TextEntry::make('artistsong' . '.' . ArtistSong::ATTRIBUTE_AS)
                            ->label(__('filament.fields.artist.songs.as.name'))
                            ->visible(fn (TextEntry $component) => $component->getLivewire() instanceof ViewTheme),

                        TextEntry::make('artistsong' . '.' . ArtistSong::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.artist.songs.alias.name'))
                            ->visible(fn (TextEntry $component) => $component->getLivewire() instanceof ViewTheme),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->visible(fn (Section $component) => $component->getLivewire() instanceof ViewArtist)
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
                        PerformanceArtistRelationManager::class,
                        GroupPerformanceArtistRelationManager::class,
                        ResourceArtistRelationManager::class,
                        MemberArtistRelationManager::class,
                        GroupArtistRelationManager::class,
                        ImageArtistRelationManager::class,
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
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [
                ActionGroup::make([
                    AttachImageAction::make('attach-artist-image'),

                    AttachArtistResourceAction::make('attach-artist-resource'),
                ])
            ],
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
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListArtists::route('/'),
            'create' => CreateArtist::route('/create'),
            'view' => ViewArtist::route('/{record:artist_id}'),
            'edit' => EditArtist::route('/{record:artist_id}/edit'),
        ];
    }
}
