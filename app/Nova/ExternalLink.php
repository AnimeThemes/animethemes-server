<?php

namespace App\Nova;

use App\Enums\ResourceType;
use App\Rules\ResourceTypeDomain;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;

class ExternalLink extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Resource::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'resource_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'resource_id', 'link', 'label'
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
            ID::make('resource_id')
                ->sortable(),

            Enum::make('type')
                ->attachEnum(ResourceType::class)
                ->sortable()
                ->rules('required', new EnumValue(ResourceType::class, false)),

            Text::make('link')
                ->sortable()
                ->rules('required', 'max:192', 'url', new ResourceTypeDomain($request->input('type')))
                ->creationRules('unique:resource,link')
                ->updateRules('unique:series,link,{{resourceId}},resource_id'),

            Text::make('label')
                ->sortable()
                ->rules('nullable', 'max:192'),

            BelongsToMany::make('Artists'),

            BelongsToMany::make('Anime'),
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
