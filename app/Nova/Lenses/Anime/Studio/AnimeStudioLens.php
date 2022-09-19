<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Nova\Actions\Models\Wiki\Anime\BackfillAnimeAction;
use App\Nova\Lenses\Anime\AnimeLens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeStudioLens.
 */
class AnimeStudioLens extends AnimeLens
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
        return __('nova.lenses.anime.studios.name');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_STUDIOS);
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
            (new BackfillAnimeAction($request->user()))
                ->confirmButtonText(__('nova.actions.anime.backfill.confirmButtonText'))
                ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                ->showInline()
                ->canSee(function (Request $request) {
                    $user = $request->user();

                    return $user instanceof User && $user->can('update anime');
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
        return 'anime-studio-lens';
    }
}
