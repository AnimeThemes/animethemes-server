<?php declare(strict_types=1);

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;

/**
 * Class Resource
 * @package App\Nova
 */
abstract class Resource extends NovaResource
{
    /**
     * Build an "index" query for the given resource.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param NovaRequest $request
     * @param \Laravel\Scout\Builder $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query): \Laravel\Scout\Builder
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function detailQuery(NovaRequest $request, $query): Builder
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return parent::relatableQuery($request, $query);
    }
}
