<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video as VideoModel;
use App\Nova\Lenses\Video\VideoAudioLens;
use App\Nova\Lenses\Video\VideoResolutionLens;
use App\Nova\Lenses\Video\VideoSourceLens;
use App\Nova\Lenses\Video\VideoUnlinkedLens;
use App\Nova\Metrics\Video\NewVideos;
use App\Nova\Metrics\Video\VideosPerDay;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Laravel\Nova\Card;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Video.
 */
class Video extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = VideoModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = VideoModel::ATTRIBUTE_FILENAME;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.videos');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.video');
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(VideoModel::ATTRIBUTE_FILENAME),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), VideoModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Number::make(__('nova.resolution'), VideoModel::ATTRIBUTE_RESOLUTION)
                ->sortable()
                ->min(360)
                ->max(1080)
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.video_resolution_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.nc'), VideoModel::ATTRIBUTE_NC)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_nc_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.subbed'), VideoModel::ATTRIBUTE_SUBBED)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_subbed_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.lyrics'), VideoModel::ATTRIBUTE_LYRICS)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_lyrics_help'))
                ->showOnPreview()
                ->filterable(),

            Boolean::make(__('nova.uncen'), VideoModel::ATTRIBUTE_UNCEN)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_uncen_help'))
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.overlap'), VideoModel::ATTRIBUTE_OVERLAP)
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->nullable()
                ->sortable()
                ->rules(['nullable', new EnumValue(VideoOverlap::class, false)])
                ->help(__('nova.video_overlap_help'))
                ->showOnPreview()
                ->filterable(),

            Select::make(__('nova.source'), VideoModel::ATTRIBUTE_SOURCE)
                ->options(VideoSource::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->nullable()
                ->sortable()
                ->rules(['nullable', new EnumValue(VideoSource::class, false)])
                ->help(__('nova.video_source_help'))
                ->showOnPreview()
                ->filterable(),

            BelongsTo::make(__('nova.audio'), VideoModel::RELATION_AUDIO, Audio::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            BelongsToMany::make(__('nova.anime_theme_entries'), VideoModel::RELATION_ANIMETHEMEENTRIES, Entry::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->fields(fn () => [
                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            Panel::make(__('nova.file_properties'), $this->fileProperties())
                ->collapsable(),

            Panel::make(__('nova.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    protected function fileProperties(): array
    {
        return [
            Text::make(__('nova.basename'), VideoModel::ATTRIBUTE_BASENAME)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.filename'), VideoModel::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.path'), VideoModel::ATTRIBUTE_PATH)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.size'), VideoModel::ATTRIBUTE_SIZE)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.mimetype'), VideoModel::ATTRIBUTE_MIMETYPE)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return array_merge(
            parent::cards($request),
            [
                (new NewVideos())->width(Card::ONE_HALF_WIDTH),
                (new VideosPerDay())->width(Card::ONE_HALF_WIDTH),
            ]
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new VideoAudioLens(),
                new VideoResolutionLens(),
                new VideoSourceLens(),
                new VideoUnlinkedLens(),
            ]
        );
    }
}
