<?php

namespace App\Nova;

use App\Enums\Season;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;
use Yassi\NestedForm\NestedForm;

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
        return __('nova.anime');
    }

    public static function singularLabel()
    {
        return __('nova.anime');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::kebab(class_basename(get_called_class()));
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name'
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

            new Panel(__('nova.timestamps'), $this->timestamps()),

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
                ->min(1960)
                ->max(date('Y') + 1)
                ->rules('required', 'digits:4', 'integer')
                ->help(__('nova.anime_year_help')),

            Enum::make(__('nova.season'), 'season')
                ->attachEnum(Season::class)
                ->sortable()
                ->rules('required', new EnumValue(Season::class, false))
                ->help(__('nova.anime_season_help')),

            NestedForm::make(__('nova.synonyms'), 'Synonyms', Synonym::class),

            NestedForm::make(__('nova.themes'), 'Themes', Theme::class),

            NestedForm::make(__('nova.external_resources'), 'ExternalResources', ExternalResource::class),

            HasMany::make(__('nova.synonyms'), 'Synonyms'),

            HasMany::make(__('nova.themes'), 'Themes'),

            BelongsToMany::make(__('nova.series'), 'Series')
                ->searchable(),

            BelongsToMany::make(__('nova.external_resources'), 'ExternalResources')
                ->searchable(),

            AuditableLog::make(),
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
            (new Metrics\NewAnime)->width('1/2'),
            (new Metrics\AnimePerDay)->width('1/2'),
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
            new Filters\AnimeSeasonFilter,
            new Filters\AnimeYearFilter,
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
            new Lenses\AnimeAniDbResourceLens,
            new Lenses\AnimeAnilistResourceLens,
            new Lenses\AnimePlanetResourceLens,
            new Lenses\AnimeAnnResourceLens,
            new Lenses\AnimeKitsuResourceLens,
            new Lenses\AnimeMalResourceLens,
            new Lenses\AnimeSeasonYearLens,
            new Lenses\AnimeThemeLens
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
