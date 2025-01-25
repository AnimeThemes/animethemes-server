<?php

declare(strict_types=1);

namespace App\Filament\Providers;

use App\Filament\Resources\BaseResource;
use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder as ScoutBuilder;

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
            /** @var class-string<BaseResource> $resource*/
            if (!$resource::canGloballySearch() || !in_array(Searchable::class, class_uses_recursive($resource::getModel()))) {
                continue;
            }

            $query = preg_replace('/[^A-Za-z0-9 ]/', '', $query);

            /** @var ScoutBuilder $scoutBuilder */
            $scoutBuilder = $resource::getModel()::search($query);

            $resourceResults = $scoutBuilder
                ->query(fn (Builder $query) => $query->with($resource::getEloquentQuery()->getEagerLoads()))
                ->get()
                ->map(function (Model $record) use ($resource): ?GlobalSearchResult {
                    $url = $resource::getUrl('view', ['record' => $record]);

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