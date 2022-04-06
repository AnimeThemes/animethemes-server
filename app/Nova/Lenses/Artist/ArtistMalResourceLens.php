<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\Artist\CreateExternalResourceSiteForArtistAction;
use App\Nova\Lenses\BaseLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ArtistMalResourceLens.
 */
class ArtistMalResourceLens extends BaseLens
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
        return __('nova.artist_resource_lens', ['site' => ResourceSite::getDescription(ResourceSite::MAL)]);
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
            $query->whereDoesntHave(Artist::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            })
        ));
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
            ID::make(__('nova.id'), Artist::ATTRIBUTE_ID)
                ->sortable(),

            Text::make(__('nova.name'), Artist::ATTRIBUTE_NAME)
                ->sortable()
                ->filterable(),

            Text::make(__('nova.slug'), Artist::ATTRIBUTE_SLUG)
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
        return [
            (new CreateExternalResourceSiteForArtistAction(ResourceSite::MAL))->canSee(function (Request $request) {
                $user = $request->user();

                return $user->hasCurrentTeamPermission('resource:create');
            }),
        ];
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
        return 'artist-mal-resource-lens';
    }
}
