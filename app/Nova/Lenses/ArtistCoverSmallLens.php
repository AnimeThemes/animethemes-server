<?php

declare(strict_types=1);

namespace App\Nova\Lenses;

use App\Enums\Models\Wiki\ImageFacet;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

/**
 * Class ArtistCoverSmallLens.
 */
class ArtistCoverSmallLens extends Lens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return array|string|null
     */
    public function name(): array | string | null
    {
        return __('nova.artist_image_lens', ['facet' => ImageFacet::getDescription(ImageFacet::COVER_SMALL)]);
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param LensRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function query(LensRequest $request, $query): Builder
    {
        return $request->withOrdering($request->withFilters(
            $query->whereDoesntHave('images', function (Builder $imageQuery) {
                $imageQuery->where('facet', ImageFacet::COVER_SMALL);
            })
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'artist_id')
                ->sortable(),

            Text::make(__('nova.name'), 'name')
                ->sortable(),

            Text::make(__('nova.slug'), 'slug')
                ->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [
            new CreatedStartDateFilter(),
            new CreatedEndDateFilter(),
            new UpdatedStartDateFilter(),
            new UpdatedEndDateFilter(),
            new DeletedStartDateFilter(),
            new DeletedEndDateFilter(),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey(): string
    {
        return 'artist-cover-small-lens';
    }
}