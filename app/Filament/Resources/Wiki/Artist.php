<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
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
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Artist.
 */
class Artist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->helperText(__('filament.fields.artist.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->live(true)
                    ->partiallyRenderComponentsAfterStateUpdated([ArtistModel::ATTRIBUTE_SLUG])
                    ->afterStateUpdated(fn (string $state, Set $set) => $set(ArtistModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),
                // ->afterStateUpdatedJs(<<<'JS'
                //     $set('slug', slug($state ?? ''));
                // JS),

                Slug::make(ArtistModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name'))
                    ->helperText(__('filament.fields.artist.slug.help')),

                MarkdownEditor::make(ArtistModel::ATTRIBUTE_INFORMATION)
                    ->label(__('filament.fields.artist.information.name'))
                    ->helperText(__('filament.fields.artist.information.help'))
                    ->columnSpan(2)
                    ->maxLength(65535),
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

                TextColumn::make(ArtistMember::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.members.as.name'))
                    ->visibleOn([MemberArtistRelationManager::class, GroupArtistRelationManager::class]),

                TextColumn::make(ArtistMember::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.artist.members.alias.name'))
                    ->visibleOn([MemberArtistRelationManager::class, GroupArtistRelationManager::class]),

                TextColumn::make(ArtistMember::ATTRIBUTE_NOTES)
                    ->label(__('filament.fields.artist.members.notes.name'))
                    ->visibleOn([MemberArtistRelationManager::class, GroupArtistRelationManager::class]),
            ])
            ->searchable();
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
                            ->hidden(fn ($livewire) => $livewire instanceof ViewTheme)
                            ->columnSpanFull(),

                        TextEntry::make('artistsong'.'.'.ArtistSong::ATTRIBUTE_AS)
                            ->label(__('filament.fields.artist.songs.as.name'))
                            ->visible(fn (TextEntry $component) => $component->getLivewire() instanceof ViewTheme),

                        TextEntry::make('artistsong'.'.'.ArtistSong::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.artist.songs.alias.name'))
                            ->visible(fn (TextEntry $component) => $component->getLivewire() instanceof ViewTheme),
                    ])
                    ->columns(3),

                TimestampSection::make()
                    ->visible(fn (Section $component) => $component->getLivewire() instanceof ViewArtist),
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
                PerformanceArtistRelationManager::class,
                GroupPerformanceArtistRelationManager::class,
                ResourceArtistRelationManager::class,
                MemberArtistRelationManager::class,
                GroupArtistRelationManager::class,
                ImageArtistRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            AttachArtistResourceAction::make('attach-artist-resource'),
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
            'index' => ListArtists::route('/'),
            'view' => ViewArtist::route('/{record:artist_id}'),
        ];
    }
}
