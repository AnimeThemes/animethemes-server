<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Actions\Wiki\Artist\CreateExternalResourceSiteForArtistAction;
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
 * Class ArtistAniDbResourceLens.
 */
class ArtistAniDbResourceLens extends Lens
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
        return __('nova.artist_resource_lens', ['site' => ResourceSite::getDescription(ResourceSite::ANIDB)]);
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
            $query->whereDoesntHave('resources', function (Builder $resourceQuery) {
                $resourceQuery->where('site', ResourceSite::ANIDB);
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
     * Get the filters available for the lens.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            parent::filters($request),
            [
                new CreatedStartDateFilter(),
                new CreatedEndDateFilter(),
                new UpdatedStartDateFilter(),
                new UpdatedEndDateFilter(),
                new DeletedStartDateFilter(),
                new DeletedEndDateFilter(),
            ]
        );
    }

    /**
     * Get the actions available on the lens.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(Request $request): array
    {
        return [
            (new CreateExternalResourceSiteForArtistAction(ResourceSite::ANIDB))->canSee(function (Request $request) {
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
        return 'artist-anidb-resource-lens';
    }
}