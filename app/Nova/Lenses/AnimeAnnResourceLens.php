<?php

namespace App\Nova\Lenses;

use App\Enums\ResourceType;
use App\Models\Anime;
use App\Nova\Actions\CreateExternalResourceTypeForAnimeAction;
use App\Nova\Filters\AnimeYearFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class AnimeAnnResourceLens extends Lens
{

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.anime_resource_lens', ["type" => ResourceType::getDescription(ResourceType::ANN)]);
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
        return Anime::whereDoesntHave('externalResources', function ($resource_query) {
            $resource_query->where('type', ResourceType::ANN);
        });
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
        return [
            (new CreateExternalResourceTypeForAnimeAction(ResourceType::ANN))->canSee(function ($request) {
                return $request->user()->isContributor() || $request->user()->isAdmin();
            }),
        ];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'anime-ann-resource-lens';
    }
}
