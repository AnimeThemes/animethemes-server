<?php

namespace App\Nova;

use App\Enums\SourceType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use SimpleSquid\Nova\Fields\Enum\Enum;

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

            Boolean::make(__('nova.trans'), 'trans')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_trans_help')),

            Boolean::make(__('nova.over'), 'over')
                ->sortable()
                ->rules('nullable', 'boolean')
                ->help(__('nova.video_over_help')),

            Enum::make(__('nova.source'), 'source')
                ->attachEnum(SourceType::class)
                ->sortable()
                ->rules('nullable', new EnumValue(SourceType::class, false))
                ->help(__('nova.video_source_help')),

            BelongsToMany::make(__('nova.entries'), 'Entries')
                ->searchable(),
        ];
    }

    protected function videoProperties() {
        return [
            Text::make(__('nova.basename'), 'basename')
                ->sortable()
                ->readonly(),

            Text::make(__('nova.filename'), 'filename')
                ->sortable()
                ->readonly(),

            Text::make(__('nova.path'), 'path')
                ->sortable()
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
        return [];
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
            new Filters\VideoTransFilter,
            new Filters\VideoOverFilter,
            new Filters\VideoSourceTypeFilter
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
        return [];
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
