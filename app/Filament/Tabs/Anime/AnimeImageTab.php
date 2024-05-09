<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeImageTab.
 */
abstract class AnimeImageTab extends BaseTab
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
        return __('filament.tabs.anime.images.name', ['facet' => static::facet()->localize()]);
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_IMAGES, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, static::facet()->value);
        });
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Anime::query()->whereDoesntHave(Anime::RELATION_IMAGES, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, static::facet()->value);
        })->count();
    }
}