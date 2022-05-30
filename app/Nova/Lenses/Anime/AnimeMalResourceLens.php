<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\Anime\CreateExternalResourceSiteForAnimeAction;
use App\Nova\Lenses\BaseLens;
use BenSampo\Enum\Enum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeMalResourceLens.
 */
class AnimeMalResourceLens extends BaseLens
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
        return __('nova.anime_resource_lens', ['site' => ResourceSite::getDescription(ResourceSite::MAL)]);
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
            $query->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
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
            ID::make(__('nova.id'), Anime::ATTRIBUTE_ID)
                ->sortable(),

            Text::make(__('nova.name'), Anime::ATTRIBUTE_NAME)
                ->sortable()
                ->filterable(),

            Text::make(__('nova.slug'), Anime::ATTRIBUTE_SLUG)
                ->sortable(),

            Number::make(__('nova.year'), Anime::ATTRIBUTE_YEAR)
                ->sortable()
                ->filterable(),

            Select::make(__('nova.season'), Anime::ATTRIBUTE_SEASON)
                ->options(AnimeSeason::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
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
            (new CreateExternalResourceSiteForAnimeAction(ResourceSite::MAL))->canSee(function (Request $request) {
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
        return 'anime-mal-resource';
    }
}
