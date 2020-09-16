<?php

namespace App\Nova;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

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
     * @var string
     */
    public static function group() {
        return __('nova.wiki');
    }

    public static function label()
    {
        return __('nova.videos');
    }

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
        'filename'
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
                ->sortable(),

            new Panel(__('nova.video_metadata'), $this->videoProperties()),

            new Panel(__('nova.timestamps'), $this->timestamps()),

            Number::make(__('nova.resolution'), 'resolution')
                ->sortable()
                ->min(360)
                ->max(1080)
                ->rules('nullable', 'integer')
                ->help(__('nova.video_resolution_help')),

            Boolean::make(__('nova.nc'), 'nc')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_nc_help')),

            Boolean::make(__('nova.subbed'), 'subbed')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_subbed_help')),

            Boolean::make(__('nova.lyrics'), 'lyrics')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_lyrics_help')),

            Boolean::make(__('nova.uncen'), 'uncen')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_uncen_help')),

            Select::make(__('nova.overlap'), 'overlap')
                ->options(OverlapType::asSelectArray())
                ->resolveUsing(function ($enum) {
                    return $enum ? $enum->value : null;
                })
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('nullable', new EnumValue(OverlapType::class, false))
                ->help(__('nova.video_overlap_help')),

            Select::make(__('nova.source'), 'source')
                ->options(SourceType::asSelectArray())
                ->resolveUsing(function ($enum) {
                    return $enum ? $enum->value : null;
                })
                ->displayUsing(function ($enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable()
                ->rules('nullable', new EnumValue(SourceType::class, false))
                ->help(__('nova.video_source_help')),

            BelongsToMany::make(__('nova.entries'), 'Entries', Entry::class)
                ->searchable(),

            AuditableLog::make(),
        ];
    }

    protected function videoProperties() {
        return [
            Text::make(__('nova.basename'), 'basename')
                ->hideFromIndex()
                ->readonly(),

            Text::make(__('nova.filename'), 'filename')
                ->sortable()
                ->readonly(),

            Text::make(__('nova.path'), 'path')
                ->hideFromIndex()
                ->readonly(),
        ];
    }

    protected function timestamps() {
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
            new Filters\VideoSourceTypeFilter,
            new Filters\VideoTypeFilter,
            new Filters\RecentlyCreatedFilter,
            new Filters\RecentlyUpdatedFilter
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
            new Lenses\VideoSourceLens
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
