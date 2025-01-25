<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistImageTab.
 */
abstract class ArtistImageTab extends BaseTab
{
    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    abstract protected static function facet(): ImageFacet;

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.artist.images.name', ['facet' => static::facet()->localize()]);
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Artist::RELATION_IMAGES, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, static::facet()->value);
        });
    }
}
