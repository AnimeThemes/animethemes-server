<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Studio;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\Studio\CreateExternalResourceSiteForStudioAction;
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
 * Class StudioAnilistResourceLens.
 */
class StudioAnilistResourceLens extends Lens
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
        return __('nova.studio_resource_lens', ['site' => ResourceSite::getDescription(ResourceSite::ANILIST)]);
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
            $query->whereDoesntHave(Studio::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST);
            })
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), Studio::ATTRIBUTE_ID)
                ->sortable(),

            Text::make(__('nova.name'), Studio::ATTRIBUTE_NAME)
                ->sortable(),

            Text::make(__('nova.slug'), Studio::ATTRIBUTE_SLUG)
                ->sortable(),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  Request  $request
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(Request $request): array
    {
        return [
            (new CreateExternalResourceSiteForStudioAction(ResourceSite::ANILIST))->canSee(function (Request $request) {
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
        return 'studio-anilist-resource-lens';
    }
}
