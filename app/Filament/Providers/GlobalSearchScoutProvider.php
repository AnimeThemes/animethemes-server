<?php

declare(strict_types=1);

namespace App\Filament\Providers;

use App\Filament\Resources\BaseResource;
use App\Search\Criteria;
use App\Search\Search;
use Elastic\ScoutDriverPlus\Searchable;
use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GlobalSearchScoutProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = GlobalSearchResults::make();

        foreach (Filament::getResources() as $resource) {
            /** @var class-string<BaseResource> $resource */
            if (! $resource::canGloballySearch()) {
                continue;
            }

            $modelClass = $resource::getModel();

            if (! in_array(Searchable::class, class_uses_recursive($modelClass))) {
                continue;
            }

            $resourceResults = collect(
                Search::search($modelClass, new Criteria($this->escapeReservedChars($query)))
                    ->passToEloquentBuilder(fn (Builder $builder) => $builder->with($resource::getEloquentQuery()->getEagerLoads()))
                    ->execute()
                    ->items()
            )
                ->map(function (Model $record) use ($resource): ?GlobalSearchResult {
                    $url = $resource::getUrl('view', ['record' => $record]);

                    if (blank($url)) {
                        return null;
                    }

                    return new GlobalSearchResult(
                        $resource::getGlobalSearchResultTitle($record),
                        $url,
                        $resource::getGlobalSearchResultDetails($record),
                        $resource::getGlobalSearchResultActions($record),
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
