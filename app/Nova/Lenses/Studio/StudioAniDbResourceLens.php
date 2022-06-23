<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Nova\Actions\Wiki\Studio\CreateExternalResourceSiteForStudioAction;
use App\Nova\Lenses\BaseLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class StudioAniDbResourceLens.
 */
class StudioAniDbResourceLens extends BaseLens
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
        return __('nova.studio_resource_lens', ['site' => ResourceSite::getDescription(ResourceSite::ANIDB)]);
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Studio::RELATION_RESOURCES, function (Builder $resourceQuery) {
            $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB);
        });
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
            ID::make(__('nova.id'), Studio::ATTRIBUTE_ID)
                ->sortable(),

            Text::make(__('nova.name'), Studio::ATTRIBUTE_NAME)
                ->sortable()
                ->filterable(),

            Text::make(__('nova.slug'), Studio::ATTRIBUTE_SLUG)
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
            (new CreateExternalResourceSiteForStudioAction(ResourceSite::ANIDB))->canSee(function (Request $request) {
                $user = $request->user();

                return $user->can('create external resource');
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
        return 'studio-anidb-resource-lens';
    }
}
