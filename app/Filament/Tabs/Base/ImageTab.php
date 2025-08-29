<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Base;

use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Builder;

abstract class ImageTab extends BaseTab
{
    /**
     * The image facet.
     */
    abstract protected static function facet(): ImageFacet;

    public function getLabel(): string
    {
        return __('filament.tabs.base.images.name', ['facet' => static::facet()->localize()]);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(HasImages::IMAGES_RELATION, function (Builder $imageQuery) {
            $imageQuery->where(Image::ATTRIBUTE_FACET, static::facet()->value);
        });
    }
}
