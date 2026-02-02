<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Wiki\Song\AttachSongResourceAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song\Pages\ListSongs;
use App\Filament\Resources\Wiki\Song\Pages\ViewSong;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Song;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SongResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Song::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.song');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.songs');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedMusicalNote;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'songs';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Song::ATTRIBUTE_TITLE;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Song::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->helperText(__('filament.fields.song.title.help'))
                    ->required()
                    ->maxLength(192),

                TextInput::make(Song::ATTRIBUTE_TITLE_NATIVE)
                    ->label(__('filament.fields.song.title_native.name'))
                    ->helperText(__('filament.fields.song.title_native.help'))
                    ->maxLength(192),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Song::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Song::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->copyableWithMessage(),

                TextColumn::make(Song::ATTRIBUTE_TITLE_NATIVE)
                    ->label(__('filament.fields.song.title_native.name'))
                    ->copyableWithMessage(),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Song::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Song::ATTRIBUTE_TITLE)
                            ->label(__('filament.fields.song.title.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(Song::ATTRIBUTE_TITLE_NATIVE)
                            ->label(__('filament.fields.song.title_native.name'))
                            ->copyableWithMessage(),
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
                    TextConstraint::make(Song::ATTRIBUTE_TITLE)
                        ->label(__('filament.fields.song.title.name')),

                    TextConstraint::make(Song::ATTRIBUTE_TITLE_NATIVE)
                        ->label(__('filament.fields.song.title_native.name')),

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
            AttachSongResourceAction::make(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                PerformanceSongRelationManager::class,
                ThemeSongRelationManager::class,
                ResourceRelationManager::class,

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
            'index' => ListSongs::route('/'),
            'view' => ViewSong::route('/{record:song_id}'),
        ];
    }
}
