<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Artist\Pages\ListArtists;
use App\Filament\Resources\Wiki\Artist\Pages\ViewArtist;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\MemberArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Models\Wiki\Artist as ArtistModel;
use App\Pivots\Wiki\ArtistSong;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Artist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ArtistModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.artist');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.artists');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedUserCircle;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'artists';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ArtistModel::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->helperText(__('filament.fields.artist.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

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
            ])
            ->searchable();
    }

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
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                PerformanceArtistRelationManager::class,
                GroupPerformanceArtistRelationManager::class,
                ResourceRelationManager::class,
                MemberArtistRelationManager::class,
                GroupArtistRelationManager::class,
                ImageRelationManager::class,

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
            AttachArtistResourceAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListArtists::route('/'),
            'view' => ViewArtist::route('/{record:artist_id}'),
        ];
    }
}
