<?php

declare(strict_types=1);

namespace App\Filament\Providers;

use App\Filament\Resources\BaseResource;
use Elastic\ScoutDriverPlus\Searchable;
use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
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
            /** @var class-string<BaseResource> $resource */
            if (! $resource::canGloballySearch() || ! in_array(Searchable::class, class_uses_recursive($resource::getModel()))) {
                continue;
            }

            $query = $this->escapeReservedChars($query);

            /** @var ScoutBuilder $scoutBuilder */
            /** @phpstan-ignore-next-line */
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

            if (! $resourceResults->count()) {
                continue;
            }

            $builder->category($resource::getPluralModelLabel(), $resourceResults);
        }

        return $builder;
    }

    /**
     * Prepare the search query for Elasticsearch.
     *
     * @param  string  $search
     * @return string
     */
    public function escapeReservedChars(string $search): string
    {
        return preg_replace(
            [
                '_[<>]+_',
                '_[-+=!(){}[\]^"~*?:\\/\\\\]|&(?=&)|\|(?=\|)_',
            ],
            [
                '',
                '\\\\$0',
            ],
            $search
        );
    }
}
