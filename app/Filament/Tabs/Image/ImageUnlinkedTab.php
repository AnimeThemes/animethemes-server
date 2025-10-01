<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

class ImageUnlinkedTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'image-unlinked-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.image.unlinked.name');
    }

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
