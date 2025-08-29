<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Base;

use App\Contracts\Models\HasResources;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

abstract class ResourceTab extends BaseTab
{
    abstract protected static function site(): ResourceSite;

    public function getLabel(): string
    {
        return __('filament.tabs.base.resources.name', ['site' => static::site()->localize()]);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(HasResources::RESOURCES_RELATION, function (Builder $resourceQuery) {
            $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, static::site()->value);
        });
    }
}
