<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Video;
use App\Nova\Lenses\BaseLens;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class VideoUnlinkedLens.
 */
class VideoUnlinkedLens extends BaseLens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.video_unlinked_lens');
    }

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
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Video::RELATION_ANIMETHEMEENTRIES)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE, 'misc%');
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), Video::ATTRIBUTE_ID)
                ->sortable(),

            Text::make(__('nova.filename'), Video::ATTRIBUTE_FILENAME)
                ->sortable()
                ->filterable(),

            Number::make(__('nova.resolution'), Video::ATTRIBUTE_RESOLUTION)
                ->sortable()
                ->filterable(),

            Boolean::make(__('nova.nc'), Video::ATTRIBUTE_NC)
                ->sortable()
                ->filterable(),

            Boolean::make(__('nova.subbed'), Video::ATTRIBUTE_SUBBED)
                ->sortable()
                ->filterable(),

            Boolean::make(__('nova.lyrics'), Video::ATTRIBUTE_LYRICS)
                ->sortable()
                ->filterable(),

            Boolean::make(__('nova.uncen'), Video::ATTRIBUTE_UNCEN)
                ->sortable()
                ->filterable(),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'video-unlinked-lens';
    }
}
