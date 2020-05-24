<?php

namespace App\Nova\Lenses;

use App\Enums\Season;
use App\Models\Anime;
use App\Nova\Filters\AnimeSeasonFilter;
use App\Nova\Filters\AnimeYearFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;
use SimpleSquid\Nova\Fields\Enum\Enum;

class AnimeThemeLens extends Lens
{

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.anime_theme_lens');
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return Anime::whereDoesntHave('themes');
    }

    /**
     * Get the fields available to the lens.
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
                ->sortable(),

            Text::make(__('nova.alias'), 'alias')
                ->sortable(),

            Number::make(__('nova.year'), 'year')
                ->sortable(),

            Enum::make(__('nova.season'), 'season')
                ->attachEnum(Season::class)
                ->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new AnimeSeasonFilter,
            new AnimeYearFilter,
            new RecentlyCreatedFilter,
            new RecentlyUpdatedFilter
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'anime-theme-lens';
    }
}
