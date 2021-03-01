<?php

namespace App\Nova;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
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

class Video extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Video::class;

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
    public static function group()
    {
        return __('nova.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return array|string|null
     */
    public static function label()
    {
        return __('nova.videos');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return array|string|null
     */
    public static function singularLabel()
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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'video_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            new Panel(__('nova.video_metadata'), $this->videoProperties()),

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
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->nullable()
                ->sortable()
                ->rules('nullable', (new EnumValue(VideoOverlap::class, false))->__toString())
                ->help(__('nova.video_overlap_help')),

            Select::make(__('nova.source'), 'source')
                ->options(VideoSource::asSelectArray())
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->nullable()
                ->sortable()
                ->rules('nullable', (new EnumValue(VideoSource::class, false))->__toString())
                ->help(__('nova.video_source_help')),

            BelongsToMany::make(__('nova.entries'), 'Entries', Entry::class)
                ->searchable(),

            AuditableLog::make(),
        ];
    }

    /**
     * @return array
     */
    protected function videoProperties()
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
        ];
    }

    /**
     * @return array
     */
    protected function timestamps()
    {
        return [
            DateTime::make(__('nova.created_at'), 'created_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),

            DateTime::make(__('nova.updated_at'), 'updated_at')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->readonly(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new Metrics\NewVideos)->width('1/2'),
            (new Metrics\VideosPerDay)->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\VideoNcFilter,
            new Filters\VideoSubbedFilter,
            new Filters\VideoLyricsFilter,
            new Filters\VideoUncenFilter,
            new Filters\VideoOverlapFilter,
            new Filters\VideoSourceFilter,
            new Filters\VideoTypeFilter,
            new Filters\RecentlyCreatedFilter,
            new Filters\RecentlyUpdatedFilter,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new Lenses\VideoSourceLens,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
