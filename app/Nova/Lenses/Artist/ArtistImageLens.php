<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ArtistImageLens.
 */
abstract class ArtistImageLens extends ArtistLens
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
        return __('nova.lenses.artist.images.name', ['facet' => static::facet()->localize()]);
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Artist::RELATION_IMAGES, function (Builder $imageQuery) {
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
        return [];
    }
}
