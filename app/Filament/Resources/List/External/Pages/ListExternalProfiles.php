<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\ExternalProfileResource;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Builder;

class ListExternalProfiles extends BaseListResources
{
    protected static string $resource = ExternalProfileResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, ExternalProfile::class);
    }
}
