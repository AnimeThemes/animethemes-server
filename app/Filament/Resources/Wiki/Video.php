<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Filament\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Filament\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Filament\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Pages\CreateVideo;
use App\Filament\Resources\Wiki\Video\Pages\EditVideo;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Resources\Wiki\Video\Pages\ViewVideo;
use App\Filament\Resources\Wiki\Video\RelationManagers\EntryVideoRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\TrackVideoRelationManager;
use App\Filament\TableActions\Repositories\Storage\Wiki\Video\ReconcileVideoTableAction;
use App\Filament\TableActions\Storage\Wiki\Video\UploadVideoTableAction;
use App\Models\Wiki\Audio as AudioModel;
use App\Models\Wiki\Video as VideoModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
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
        return __('filament.resources.icon.videos');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'videos';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return VideoModel::ATTRIBUTE_ID;
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

                TextInput::make(VideoModel::ATTRIBUTE_BASENAME)
                    ->label(__('filament.fields.video.basename.name'))
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(VideoModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.video.filename.name'))
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(VideoModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.video.path.name'))
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(VideoModel::ATTRIBUTE_SIZE)
                    ->label(__('filament.fields.video.size.name'))
                    ->numeric()
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(VideoModel::ATTRIBUTE_MIMETYPE)
                    ->label(__('filament.fields.video.mimetype.name'))
                    ->hiddenOn(['create', 'edit']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(VideoModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(VideoModel::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name'))
                    ->toggleable(),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name'))
                    ->toggleable(),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name'))
                    ->toggleable(),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name'))
                    ->toggleable(),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name'))
                    ->toggleable(),

                SelectColumn::make(VideoModel::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->options(VideoOverlap::asSelectArray())
                    ->toggleable(),

                SelectColumn::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->options(VideoSource::asSelectArray())
                    ->toggleable(),

                TextColumn::make(VideoModel::RELATION_AUDIO . '.' . AudioModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->visibleOn(['create', 'edit']),

                TextColumn::make(VideoModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.video.filename.name'))
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
            ])
            ->defaultSort(VideoModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions());
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
                    ]),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps()),
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
                EntryVideoRelationManager::class,
                TrackVideoRelationManager::class,
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            []
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
            [
                ActionGroup::make([
                    BackfillAudioAction::make('backfill-audio')
                        ->label(__('filament.actions.video.backfill.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::TwoExtraLarge)
                        ->authorize('update', VideoModel::class),

                    MoveVideoAction::make('move-video')
                        ->label(__('filament.actions.video.move.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', VideoModel::class),

                    DeleteVideoAction::make('delete-video')
                        ->label(__('filament.actions.video.delete.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('delete', VideoModel::class),
                ]),
            ],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                UploadVideoTableAction::make('upload-video')
                    ->label(__('filament.actions.video.upload.name'))
                    ->requiresConfirmation()
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->authorize('create', VideoModel::class),

                ReconcileVideoTableAction::make('reconcile-video')
                    ->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.videos')]))
                    ->requiresConfirmation()
                    ->authorize('create', VideoModel::class),
            ]),
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
            'index' => ListVideos::route('/'),
            'create' => CreateVideo::route('/create'),
            'view' => ViewVideo::route('/{record:video_id}'),
            'edit' => EditVideo::route("/{record:video_id}/edit"),
        ];
    }
}
