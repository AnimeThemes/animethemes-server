<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ImageUnlinkedTab.
 */
class ImageUnlinkedTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'image-unlinked-tab';
    }

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.image.unlinked.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereNot(Image::ATTRIBUTE_FACET, ImageFacet::GRILL->value)
            ->whereNot(Image::ATTRIBUTE_FACET, ImageFacet::DOCUMENT->value)
            ->whereDoesntHave(Image::RELATION_ANIME)
            ->whereDoesntHave(Image::RELATION_ARTISTS)
            ->whereDoesntHave(Image::RELATION_STUDIOS)
            ->whereDoesntHave(Image::RELATION_PLAYLISTS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Image::query()
            ->whereNot(Image::ATTRIBUTE_FACET, ImageFacet::GRILL->value)
            ->whereNot(Image::ATTRIBUTE_FACET, ImageFacet::DOCUMENT->value)
            ->whereDoesntHave(Image::RELATION_ANIME)
            ->whereDoesntHave(Image::RELATION_ARTISTS)
            ->whereDoesntHave(Image::RELATION_STUDIOS)
            ->whereDoesntHave(Image::RELATION_PLAYLISTS)
            ->count();
    }
}
