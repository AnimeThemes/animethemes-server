<?php

namespace App\Nova;

use App\Enums\SourceType;
use BenSampo\Enum\Rules\EnumValue;
use Digitalazgroup\PlainText\PlainText;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
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
    public static $title = 'video_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'video_id', 'basename', 'filename', 'path', 'resolution'
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
            ID::make('video_id')
                ->sortable(),

            PlainText::make('basename')
                ->sortable(),

            PlainText::make('filename')
                ->sortable(),

            PlainText::make('path')
                ->sortable(),

            Number::make('resolution')
                ->sortable()
                ->min(360)
                ->max(1080)
                ->rules('nullable', 'integer'), //TODO: custom rule with range

            Boolean::make('nc')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('subbed')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('lyrics')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('uncen')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('trans')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('over')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Boolean::make('over')
                ->sortable()
                ->rules('nullable', 'boolean'),

            Enum::make('source')
                ->attachEnum(SourceType::class)
                ->sortable()
                ->rules('nullable', new EnumValue(SourceType::class, false)),

            BelongsToMany::make('Entries'),
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
        return [];
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
