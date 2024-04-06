<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Pages\CreateVideo;
use App\Filament\Resources\Wiki\Video\Pages\EditVideo;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Resources\Wiki\Video\Pages\ViewVideo;
use App\Models\Wiki\Video as VideoModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
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
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->numeric()
                    ->sortable(),

                    TextColumn::make(VideoModel::ATTRIBUTE_RESOLUTION)
                    ->label(__('filament.fields.video.resolution.name'))
                    ->numeric(),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_NC)
                    ->label(__('filament.fields.video.nc.name')),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_SUBBED)
                    ->label(__('filament.fields.video.subbed.name')),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_LYRICS)
                    ->label(__('filament.fields.video.lyrics.name')),

                CheckboxColumn::make(VideoModel::ATTRIBUTE_UNCEN)
                    ->label(__('filament.fields.video.uncen.name')),

                SelectColumn::make(VideoModel::ATTRIBUTE_OVERLAP)
                    ->label(__('filament.fields.video.overlap.name'))
                    ->options(VideoOverlap::asSelectArray()),

                SelectColumn::make(VideoModel::ATTRIBUTE_SOURCE)
                    ->label(__('filament.fields.video.source.name'))
                    ->options(VideoSource::asSelectArray()),

                TextColumn::make(VideoModel::ATTRIBUTE_BASENAME)
                    ->label(__('filament.fields.video.basename.name'))
                    ->copyable()
                    ->hidden(),
                    
                TextColumn::make(VideoModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.video.filename.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(VideoModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.video.path.name'))
                    ->sortable()
                    ->copyable()
                    ->hidden(),

                TextColumn::make(VideoModel::ATTRIBUTE_SIZE)
                    ->label(__('filament.fields.video.size.name'))
                    ->numeric()
                    ->sortable()
                    ->copyable()
                    ->hidden(),

                TextColumn::make(VideoModel::ATTRIBUTE_MIMETYPE)
                    ->label(__('filament.fields.video.mimetype.name'))
                    ->sortable()
                    ->copyable()
                    ->hidden(),
            ])
            ->defaultSort(VideoModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return [];
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
            [],
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
