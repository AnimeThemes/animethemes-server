<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Song\AttachSongResourceAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song\Pages\ListSongs;
use App\Filament\Resources\Wiki\Song\Pages\ViewSong;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ResourceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Song as SongModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Song extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SongModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.song');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.songs');
    }

    /**
     * The logical group associated with the resource.
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.resources.songs');
    }

    /**
     * Determine if the resource can globally search.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function canGloballySearch(): bool
    {
        return true;
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'songs';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return SongModel::ATTRIBUTE_TITLE;
    }

    /**
     * The form to the actions.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SongModel::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->helperText(__('filament.fields.song.title.help'))
                    ->required()
                    ->maxLength(192),
            ]);
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SongModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(SongModel::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->copyableWithMessage(),
            ])
            ->searchable();
    }

    /**
     * Get the infolist available for the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SongModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(SongModel::ATTRIBUTE_TITLE)
                            ->label(__('filament.fields.song.title.name'))
                            ->copyableWithMessage(),
                    ])
                    ->columns(2),

                TimestampSection::make(),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                PerformanceSongRelationManager::class,
                ThemeSongRelationManager::class,
                ResourceSongRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            AttachSongResourceAction::make(),
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSongs::route('/'),
            'view' => ViewSong::route('/{record:song_id}'),
        ];
    }
}
