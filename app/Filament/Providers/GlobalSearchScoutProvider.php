<?php

declare(strict_types=1);

namespace App\Filament\Providers;

use App\Models\BaseModel;
use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Elastic\ScoutDriverPlus\Searchable;

/**
 * Class GlobalSearchScoutProvider.
 */
class GlobalSearchScoutProvider implements GlobalSearchProvider
{
    /**
     * Get the results for the global search.
     *
     * @param  string  $query
     * @return GlobalSearchResults|null
     */
    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = GlobalSearchResults::make();

        foreach (Filament::getResources() as $resource) {
            if (!$resource::canGloballySearch() || !in_array(Searchable::class, class_uses_recursive($resource::getModel()))) {
                continue;
            }

            $query = preg_replace('/[^A-Za-z0-9 ]/', '', $query);
            $search = $resource::getModel()::search($query);

            $resourceResults = $search
                ->get()
                ->map(function (BaseModel $record) use ($resource): ?GlobalSearchResult {
                    $url = $resource::getGlobalSearchResultUrl($record);

                    if (blank($url)) {
                        return null;
                    }

                    return new GlobalSearchResult(
                        title: $resource::getGlobalSearchResultTitle($record),
                        url: $url,
                        details: $resource::getGlobalSearchResultDetails($record),
                        actions: $resource::getGlobalSearchResultActions($record),
                    );
                })
                ->filter();

            if (!$resourceResults->count()) {
                continue;
            }

            $builder->category($resource::getPluralModelLabel(), $resourceResults);
        }

        return $builder;
    }
}