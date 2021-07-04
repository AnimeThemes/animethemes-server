<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Nova\Filters\Wiki\VideoLyricsFilter;
use App\Nova\Filters\Wiki\VideoNcFilter;
use App\Nova\Filters\Wiki\VideoOverlapFilter;
use App\Nova\Filters\Wiki\VideoSourceFilter;
use App\Nova\Filters\Wiki\VideoSubbedFilter;
use App\Nova\Filters\Wiki\VideoTypeFilter;
use App\Nova\Filters\Wiki\VideoUncenFilter;
use App\Nova\Lenses\VideoSourceLens;
use App\Nova\Lenses\VideoUnlinkedLens;
use App\Nova\Metrics\NewVideos;
use App\Nova\Metrics\VideosPerDay;
use App\Nova\Resources\Resource;
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
     * @return array|string|null
     */
    public static function group(): array | string | null
    {
        return __('nova.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label(): array | string | null
    {
        return __('nova.videos');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel(): array | string | null
    {
        return __('nova.video');
    }

    /**
     * The columns that should be searched.
     *
     * @var string[]
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

            new Panel(__('nova.file_properties'), $this->fileProperties()),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Number::make(__('nova.resolution'), 'resolution')
                ->sortable()
                ->min(360)
                ->max(1080)
                ->nullable()
                ->rules('nullable', 'integer')
                ->help(__('nova.video_resolution_help')),

            Boolean::make(__('nova.nc'), 'nc')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_nc_help')),

            Boolean::make(__('nova.subbed'), 'subbed')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_subbed_help')),

            Boolean::make(__('nova.lyrics'), 'lyrics')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_lyrics_help')),

            Boolean::make(__('nova.uncen'), 'uncen')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_uncen_help')),

            Select::make(__('nova.overlap'), 'overlap')
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum ? $enum->description : null;
                })
                ->nullable()
                ->sortable()
                ->rules('nullable', (new EnumValue(VideoOverlap::class, false))->__toString())
                ->help(__('nova.video_overlap_help')),

            Select::make(__('nova.source'), 'source')
                ->options(VideoSource::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum ? $enum->description : null;
                })
                ->nullable()
                ->sortable()
                ->rules('nullable', (new EnumValue(VideoSource::class, false))->__toString())
                ->help(__('nova.video_source_help')),

            BelongsToMany::make(__('nova.entries'), 'Entries', Entry::class)
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
        return [
            (new NewVideos())->width('1/2'),
            (new VideosPerDay())->width('1/2'),
        ];
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
        return [
            new VideoSourceLens(),
            new VideoUnlinkedLens(),
        ];
    }
}
