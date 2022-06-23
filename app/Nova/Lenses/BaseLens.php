<?php

declare(strict_types=1);

namespace App\Nova\Lenses;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

/**
 * Class BaseLens.
 */
abstract class BaseLens extends Lens
{
    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;

    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 60;

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  LensRequest  $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function query(LensRequest $request, $query): Builder
    {
        return $request->withOrdering($request->withFilters(
            static::criteria($query)
        ));
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    abstract public static function criteria(Builder $query): Builder;
}
