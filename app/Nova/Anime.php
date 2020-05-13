<?php

namespace App\Nova;

use App\Enums\Season;
use App\Rules\YearRange;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;

class Anime extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Anime::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label()
    {
        return __('nova.anime');
    }

    public static function singularLabel()
    {
        return __('nova.anime');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
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
            ID::make(__('nova.id'), 'anime_id')
                ->sortable(),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules('required', 'max:192')
                ->help(__('nova.anime_name_help')),

            Text::make(__('nova.alias'), 'alias')
                ->sortable()
                ->rules('required', 'max:192', 'alpha_dash')
                ->creationRules('unique:anime,alias')
                ->updateRules('unique:anime,alias,{{resourceId}},anime_id')
                ->help(__('nova.anime_alias_help')),

            Number::make(__('nova.year'), 'year')
                ->sortable()
                ->min(YearRange::min())
                ->max(YearRange::max())
                ->rules('required', 'digits:4', 'integer', new YearRange)
                ->help(__('nova.anime_year_help')),

            Enum::make(__('nova.season'), 'season')
                ->attachEnum(Season::class)
                ->sortable()
                ->rules('required', new EnumValue(Season::class, false))
                ->help(__('nova.anime_season_help')),

            HasMany::make(__('nova.synonyms'), 'Synonyms'),

            HasMany::make(__('nova.themes'), 'Themes'),

            BelongsToMany::make(__('nova.series'), 'Series'),

            BelongsToMany::make(__('nova.external_resources'), 'ExternalResources'),
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
