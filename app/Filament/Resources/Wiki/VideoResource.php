<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
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
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Resources\Wiki\Video\Pages\ViewVideo;
use App\Filament\Resources\Wiki\Video\RelationManagers\EntryVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\TrackVideoRelationManager;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\IconEntry;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VideoResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Video::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.video');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.videos');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
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
        return Video::ATTRIBUTE_BASENAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Video::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name'))
                    ->helperText(__('filament.fields.video.resolution.help'))
                    ->integer()
                    ->minValue(360)
                    ->maxValue(1080),

                Checkbox::make(Video::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->helperText(__('filament.fields.video.nc.help')),

                Checkbox::make(Video::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->helperText(__('filament.fields.video.subbed.help')),

                Checkbox::make(Video::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->helperText(__('filament.fields.video.lyrics.help')),

                Checkbox::make(Video::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->helperText(__('filament.fields.video.uncen.help')),

                Select::make(Video::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->helperText(__('filament.fields.video.overlap.help'))
                    ->options(VideoOverlap::class)
                    ->required(),

                Select::make(Video::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->helperText(__('filament.fields.video.source.help'))
                    ->options(VideoSource::class),

                Select::make(Video::ATTRIBUTE_AUDIO)
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->relationship(Video::RELATION_AUDIO, Audio::ATTRIBUTE_FILENAME)
                    ->searchable(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Video::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Video::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name')),

                IconColumn::make(Video::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->boolean(),

                IconColumn::make(Video::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->boolean(),

                IconColumn::make(Video::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->boolean(),

                IconColumn::make(Video::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->boolean(),

                TextColumn::make(Video::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->formatStateUsing(fn (VideoOverlap $state): ?string => $state->localize()),

                TextColumn::make(Video::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->formatStateUsing(fn (VideoSource $state): ?string => $state->localize()),

                TextColumn::make(Video::ATTRIBUTE_FILENAME)
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
                        TextEntry::make(Video::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Video::ATTRIBUTE_OVERLAP)
                            ->label(__('filament.fields.video.overlap.name'))
                            ->formatStateUsing(fn (VideoOverlap $state): ?string => $state->localize()),

                        TextEntry::make(Video::ATTRIBUTE_SOURCE)
                            ->label(__('filament.fields.video.source.name'))
                            ->formatStateUsing(fn (VideoSource $state): ?string => $state->localize()),

                        IconEntry::make(Video::ATTRIBUTE_NC)
                            ->label(__('filament.fields.video.nc.name'))
                            ->boolean(),

                        IconEntry::make(Video::ATTRIBUTE_SUBBED)
                            ->label(__('filament.fields.video.subbed.name'))
                            ->boolean(),

                        IconEntry::make(Video::ATTRIBUTE_LYRICS)
                            ->label(__('filament.fields.video.lyrics.name'))
                            ->boolean(),

                        IconEntry::make(Video::ATTRIBUTE_UNCEN)
                            ->label(__('filament.fields.video.uncen.name'))
                            ->boolean(),

                        BelongsToEntry::make(Video::RELATION_AUDIO, AudioResource::class),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.file_properties'))
                    ->schema([
                        TextEntry::make(Video::ATTRIBUTE_BASENAME)
                            ->label(__('filament.fields.video.basename.name')),

                        TextEntry::make(Video::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.video.filename.name')),

                        TextEntry::make(Video::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.video.path.name')),

                        TextEntry::make(Video::ATTRIBUTE_SIZE)
                            ->label(__('filament.fields.video.size.name')),

                        TextEntry::make(Video::ATTRIBUTE_RESOLUTION)
                            ->label(__('filament.fields.video.resolution.name')),

                        TextEntry::make(Video::ATTRIBUTE_MIMETYPE)
                            ->label(__('filament.fields.video.mimetype.name')),
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
                    NumberConstraint::make(Video::ATTRIBUTE_RESOLUTION)
                        ->label(__('filament.fields.video.resolution.name')),

                    BooleanConstraint::make(Video::ATTRIBUTE_NC)
                        ->label(__('filament.fields.video.nc.name')),

                    BooleanConstraint::make(Video::ATTRIBUTE_SUBBED)
                        ->label(__('filament.fields.video.subbed.name')),

                    BooleanConstraint::make(Video::ATTRIBUTE_LYRICS)
                        ->label(__('filament.fields.video.lyrics.name')),

                    BooleanConstraint::make(Video::ATTRIBUTE_UNCEN)
                        ->label(__('filament.fields.video.uncen.name')),

                    SelectConstraint::make(Video::ATTRIBUTE_OVERLAP)
                        ->label(__('filament.fields.video.overlap.name'))
                        ->options(VideoOverlap::class)
                        ->multiple(),

                    SelectConstraint::make(Video::ATTRIBUTE_SOURCE)
                        ->label(__('filament.fields.video.source.name'))
                        ->options(VideoSource::class)
                        ->multiple(),

                    NumberConstraint::make(Video::ATTRIBUTE_SIZE)
                        ->label(__('filament.fields.video.size.name')),

                    ...parent::getConstraints(),
                ]),

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
