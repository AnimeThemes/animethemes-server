<?php

declare(strict_types=1);

namespace App\Nova\Lenses;

use App\Enums\ImageFacet;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\ImageFacetFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use BenSampo\Enum\Enum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image as NovaImage;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

/**
 * Class ImageUnlinkedLens.
 */
class ImageUnlinkedLens extends Lens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return array|string|null
     */
    public function name(): array | string | null
    {
        return __('nova.image_unlinked_lens');
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
            $query->whereDoesntHave('anime')->whereDoesntHave('artists')
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
            ID::make(__('nova.id'), 'image_id')
                ->sortable(),

            Select::make(__('nova.facet'), 'facet')
                ->options(ImageFacet::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum ? $enum->description : null;
                })
                ->sortable(),

            NovaImage::make(__('nova.image'), 'path')
                ->disk('images'),
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
            new ImageFacetFilter(),
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
        return 'image-unlinked-lens';
    }
}
