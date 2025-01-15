<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Filament\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Filament\Actions\Storage\MoveAllAction;
use App\Filament\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Filament\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Filament\BulkActions\Models\Wiki\Video\VideoDiscordNotificationBulkAction;
use App\Filament\BulkActions\Storage\Wiki\Video\DeleteVideoBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\CheckboxFilter;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Pages\EditVideo;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Resources\Wiki\Video\Pages\ViewVideo;
use App\Filament\Resources\Wiki\Video\RelationManagers\EntryVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\TrackVideoRelationManager;
use App\Filament\TableActions\Repositories\Storage\Wiki\Video\ReconcileVideoTableAction;
use App\Filament\TableActions\Storage\Wiki\Video\UploadVideoTableAction;
use App\Models\Wiki\Audio as AudioModel;
use App\Models\Wiki\Video as VideoModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Class Video.
 */
class Video extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = VideoModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.video');
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
        return __('filament.resources.label.videos');
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
        return __('filament-icons.resources.videos');
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
        return 'videos';
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
        return VideoModel::ATTRIBUTE_BASENAME;
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
                TextInput::make(VideoModel::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name'))
                    ->helperText(__('filament.fields.video.resolution.help'))
                    ->numeric()
                    ->minValue(360)
                    ->maxValue(1080)
                    ->nullable()
                    ->rules(['nullable', 'integer']),

                Checkbox::make(VideoModel::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->helperText(__('filament.fields.video.nc.help'))
                    ->rules(['nullable', 'boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->helperText(__('filament.fields.video.subbed.help'))
                    ->rules(['nullable', 'boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->helperText(__('filament.fields.video.lyrics.help'))
                    ->rules(['nullable', 'boolean']),

                Checkbox::make(VideoModel::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->helperText(__('filament.fields.video.uncen.help'))
                    ->rules(['nullable', 'boolean']),

                Select::make(VideoModel::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->helperText(__('filament.fields.video.overlap.help'))
                    ->options(VideoOverlap::asSelectArray())
                    ->rules(['nullable', new Enum(VideoOverlap::class)]),

                Select::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->helperText(__('filament.fields.video.source.help'))
                    ->options(VideoSource::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(VideoSource::class)]),

                Select::make(VideoModel::ATTRIBUTE_AUDIO)
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->relationship(VideoModel::RELATION_AUDIO, AudioModel::ATTRIBUTE_FILENAME)
                    ->searchable(),
            ])
            ->columns(1);
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

                        TextEntry::make(VideoModel::ATTRIBUTE_MIMETYPE)
                            ->label(__('filament.fields.video.mimetype.name')),

                        TextEntry::make(VideoModel::ATTRIBUTE_RESOLUTION)
                            ->label(__('filament.fields.video.resolution.name')),
                    ])
                    ->columns(3),

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
                    [
                        EntryVideoRelationManager::class,
                        ScriptVideoRelationManager::class,
                        TrackVideoRelationManager::class,
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
            [
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
                    ->options(VideoOverlap::asSelectArray()),

                SelectFilter::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->options(VideoSource::asSelectArray()),

                NumberFilter::make(VideoModel::ATTRIBUTE_SIZE)
                    ->label(__('filament.fields.video.size.name')),
            ],
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
                    BackfillAudioAction::make('backfill-audio'),

                    MoveVideoAction::make('move-video'),

                    MoveAllAction::make('move-all'),

                    DeleteVideoAction::make('delete-video'),
                ]),
            ],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions([
                DeleteVideoBulkAction::make('delete-video')
            ]),
            [
                VideoDiscordNotificationBulkAction::make('discord-notification'),
            ],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return [
            ActionGroup::make([
                UploadVideoTableAction::make('upload-video'),

                ReconcileVideoTableAction::make('reconcile-video'),
            ]),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
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
            'index' => ListVideos::route('/'),
            'view' => ViewVideo::route('/{record:video_id}'),
            'edit' => EditVideo::route("/{record:video_id}/edit"),
        ];
    }
}
