<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\Anime\AttachAnimeResourceAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeResourceLens.
 */
abstract class AnimeResourceLens extends AnimeLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    abstract protected static function site(): ResourceSite;

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.anime_resource_lens', ['site' => static::site()->description]);
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
            $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, static::site()->value);
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
            (new AttachAnimeResourceAction(static::site()))
                ->confirmButtonText(__('nova.create'))
                ->cancelButtonText(__('nova.cancel'))
                ->showInline()
                ->canSee(function (Request $request) {
                    $user = $request->user();

                    return $user instanceof User && $user->can('create external resource');
                }),
        ];
    }
}
