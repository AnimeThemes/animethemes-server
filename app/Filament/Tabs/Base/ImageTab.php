<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Base;

use App\Contracts\Models\HasImages;
use App\Filament\Tabs\BaseTab;
use Illuminate\Database\Eloquent\Builder;

abstract class ImageTab extends BaseTab
{
    public function getLabel(): string
    {
        return __('filament.tabs.base.images.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(HasImages::IMAGES_RELATION);
    }
}
