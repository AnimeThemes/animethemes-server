<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Nova\Filters\Wiki\Video\VideoLyricsFilter;
use App\Nova\Filters\Wiki\Video\VideoNcFilter;
use App\Nova\Filters\Wiki\Video\VideoOverlapFilter;
use App\Nova\Filters\Wiki\Video\VideoSourceFilter;
use App\Nova\Filters\Wiki\Video\VideoSubbedFilter;
use App\Nova\Filters\Wiki\Video\VideoTypeFilter;
use App\Nova\Filters\Wiki\Video\VideoUncenFilter;
use App\Nova\Lenses\Video\VideoSourceLens;
use App\Nova\Lenses\Video\VideoUnlinkedLens;
use App\Nova\Metrics\Video\NewVideos;
use App\Nova\Metrics\Video\VideosPerDay;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class Video.
 */
class Video extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Wiki\Video::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'filename';

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'filename',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'video_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.file_properties'), $this->fileProperties()),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Number::make(__('nova.resolution'), 'resolution')
                ->sortable()
                ->min(360)
                ->max(1080)
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.video_resolution_help')),

            Boolean::make(__('nova.nc'), 'nc')
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_nc_help')),

            Boolean::make(__('nova.subbed'), 'subbed')
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_subbed_help')),

            Boolean::make(__('nova.lyrics'), 'lyrics')
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_lyrics_help')),

            Boolean::make(__('nova.uncen'), 'uncen')
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.video_uncen_help')),

            Select::make(__('nova.overlap'), 'overlap')
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->nullable()
                ->sortable()
                ->rules(['nullable', (new EnumValue(VideoOverlap::class, false))->__toString()])
                ->help(__('nova.video_overlap_help')),

            Select::make(__('nova.source'), 'source')
                ->options(VideoSource::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->nullable()
                ->sortable()
                ->rules(['nullable', (new EnumValue(VideoSource::class, false))->__toString()])
                ->help(__('nova.video_source_help')),

            BelongsToMany::make(__('nova.entries'), 'AnimeThemeEntries', Entry::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    /**
     * @return array
     */
    protected function fileProperties(): array
    {
        return [
            Text::make(__('nova.basename'), 'basename')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            Text::make(__('nova.filename'), 'filename')
                ->sortable()
                ->hideWhenCreating()
                ->readonly(),

            Text::make(__('nova.path'), 'path')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            Number::make(__('nova.size'), 'size')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            Text::make(__('nova.mimetype'), 'mimetype')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return array_merge(
            parent::cards($request),
            [
                (new NewVideos())->width('1/2'),
                (new VideosPerDay())->width('1/2'),
            ]
        );
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new VideoNcFilter(),
                new VideoSubbedFilter(),
                new VideoLyricsFilter(),
                new VideoUncenFilter(),
                new VideoOverlapFilter(),
                new VideoSourceFilter(),
                new VideoTypeFilter(),
            ],
            parent::filters($request)
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new VideoSourceLens(),
                new VideoUnlinkedLens(),
            ]
        );
    }
}
