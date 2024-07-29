<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Nova\Actions\Models\Wiki\Anime\AttachAnimeImageAction;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AnimeImageLens.
 */
abstract class AnimeImageLens extends AnimeLens
{
    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    abstract protected static function facet(): ImageFacet;

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.lenses.anime.images.name', ['facet' => static::facet()->localize()]);
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_IMAGES, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, static::facet()->value);
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
            (new AttachAnimeImageAction([static::facet()]))
                ->confirmButtonText(__('nova.actions.models.wiki.attach_image.confirmButtonText'))
                ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                ->exceptOnIndex()
                ->canSeeWhen('create', Image::class),
        ];
    }
}
