<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Models\Wiki\Anime\AttachAnimeResourceAction;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeStreamingResourceLens.
 */
class AnimeStreamingResourceLens extends AnimeLens
{
    /**
     * The resources site.
     *
     * @return ResourceSite[]
     */
    protected static function sites(): array
    {
        return [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];
    }

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.lenses.anime.streaming_resources.name');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
            $resourceQuery->where(function (Builder $query) {
                foreach (static::sites() as $site) {
                    $query->orWhere(ExternalResource::ATTRIBUTE_SITE, $site->value);
                }
            });
        });
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
            (new AttachAnimeResourceAction(static::sites(), __('nova.actions.models.wiki.attach_streaming_resource.name')))
                ->confirmButtonText(__('nova.actions.models.wiki.attach_resource.confirmButtonText'))
                ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                ->showInline()
                ->canSeeWhen('create', ExternalResource::class),
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
        return 'anime-streaming-resources-lens';
    }
}
