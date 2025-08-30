<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Filament\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Filament\Actions\Repositories\Storage\Wiki\Video\ReconcileVideoAction;
use App\Filament\Actions\Storage\MoveAllAction;
use App\Filament\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Filament\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Filament\Actions\Storage\Wiki\Video\UploadVideoAction;
use App\Filament\BulkActions\Models\Wiki\Video\VideoDiscordNotificationBulkAction;
use App\Filament\BulkActions\Storage\Wiki\Video\DeleteVideoBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Resources\Wiki\Video\Pages\ViewVideo;
use App\Filament\Resources\Wiki\Video\RelationManagers\EntryVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\TrackVideoRelationManager;
use App\Models\Wiki\Audio as AudioModel;
use App\Models\Wiki\Video as VideoModel;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Video extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = VideoModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.video');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.videos');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedVideoCamera;
    }

    public static function getRecordSlug(): string
    {
        return 'videos';
    }

    public static function getRecordTitleAttribute(): string
    {
        return VideoModel::ATTRIBUTE_BASENAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(VideoModel::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name'))
                    ->helperText(__('filament.fields.video.resolution.help'))
                    ->integer()
                    ->minValue(360)
                    ->maxValue(1080),

                Checkbox::make(VideoModel::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->helperText(__('filament.fields.video.nc.help'))
                    ->rules(['boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->helperText(__('filament.fields.video.subbed.help'))
                    ->rules(['boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->helperText(__('filament.fields.video.lyrics.help'))
                    ->rules(['boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->helperText(__('filament.fields.video.uncen.help'))
                    ->rules(['boolean']),

                Select::make(VideoModel::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->helperText(__('filament.fields.video.overlap.help'))
                    ->options(VideoOverlap::class),

                Select::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->helperText(__('filament.fields.video.source.help'))
                    ->options(VideoSource::class)
                    ->required(),

                Select::make(VideoModel::ATTRIBUTE_AUDIO)
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->relationship(VideoModel::RELATION_AUDIO, AudioModel::ATTRIBUTE_FILENAME)
                    ->searchable(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(VideoModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(VideoModel::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name')),

                IconColumn::make(VideoModel::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->boolean(),

                IconColumn::make(VideoModel::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->boolean(),

                IconColumn::make(VideoModel::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->boolean(),

                IconColumn::make(VideoModel::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->boolean(),

                TextColumn::make(VideoModel::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->formatStateUsing(fn (VideoOverlap $state) => $state->localize()),

                TextColumn::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->formatStateUsing(fn (VideoSource $state) => $state->localize()),

                TextColumn::make(VideoModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.video.filename.name'))
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
                        TextEntry::make(VideoModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(VideoModel::ATTRIBUTE_OVERLAP)
                            ->label(__('filament.fields.video.overlap.name'))
                            ->formatStateUsing(fn (VideoOverlap $state) => $state->localize()),

                        TextEntry::make(VideoModel::ATTRIBUTE_SOURCE)
                            ->label(__('filament.fields.video.source.name'))
                            ->formatStateUsing(fn (VideoSource $state) => $state->localize()),

                        IconEntry::make(VideoModel::ATTRIBUTE_NC)
                            ->label(__('filament.fields.video.nc.name'))
                            ->boolean(),

                        IconEntry::make(VideoModel::ATTRIBUTE_SUBBED)
                            ->label(__('filament.fields.video.subbed.name'))
                            ->boolean(),

                        IconEntry::make(VideoModel::ATTRIBUTE_LYRICS)
                            ->label(__('filament.fields.video.lyrics.name'))
                            ->boolean(),

                        IconEntry::make(VideoModel::ATTRIBUTE_UNCEN)
                            ->label(__('filament.fields.video.uncen.name'))
                            ->boolean(),

                        BelongsToEntry::make(VideoModel::RELATION_AUDIO, Audio::class),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.file_properties'))
                    ->schema([
                        TextEntry::make(VideoModel::ATTRIBUTE_BASENAME)
                            ->label(__('filament.fields.video.basename.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.video.filename.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.video.path.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_SIZE)
                            ->label(__('filament.fields.video.size.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_RESOLUTION)
                            ->label(__('filament.fields.video.resolution.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_MIMETYPE)
                            ->label(__('filament.fields.video.mimetype.name')),
                    ])
                    ->columns(3),

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
                EntryVideoRelationManager::class,
                ScriptVideoRelationManager::class,
                TrackVideoRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            NumberFilter::make(VideoModel::ATTRIBUTE_RESOLUTION)
                ->label(__('filament.fields.video.resolution.name')),

            CheckboxFilter::make(VideoModel::ATTRIBUTE_NC)
                ->label(__('filament.fields.video.nc.name')),

            CheckboxFilter::make(VideoModel::ATTRIBUTE_SUBBED)
                ->label(__('filament.fields.video.subbed.name')),

            CheckboxFilter::make(VideoModel::ATTRIBUTE_LYRICS)
                ->label(__('filament.fields.video.lyrics.name')),

            CheckboxFilter::make(VideoModel::ATTRIBUTE_UNCEN)
                ->label(__('filament.fields.video.uncen.name')),

            SelectFilter::make(VideoModel::ATTRIBUTE_OVERLAP)
                ->label(__('filament.fields.video.overlap.name'))
                ->options(VideoOverlap::class),

            SelectFilter::make(VideoModel::ATTRIBUTE_SOURCE)
                ->label(__('filament.fields.video.source.name'))
                ->options(VideoSource::class),

            NumberFilter::make(VideoModel::ATTRIBUTE_SIZE)
                ->label(__('filament.fields.video.size.name')),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            BackfillAudioAction::make(),

            MoveVideoAction::make(),

            MoveAllAction::make(),

            DeleteVideoAction::make(),
        ];
    }

    /**
     * @param  array<int, ActionGroup|\Filament\Actions\Action>|null  $actionsIncludedInGroup
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions([
                DeleteVideoBulkAction::make(),
            ]),

            VideoDiscordNotificationBulkAction::make(),
        ];
    }

    /**
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [
            UploadVideoAction::make(),

            ActionGroup::make([
                ReconcileVideoAction::make(),
            ]),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListVideos::route('/'),
            'view' => ViewVideo::route('/{record:video_id}'),
        ];
    }
}
