<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\Actions\Base\CreateAction;
use App\Search\Criteria;
use App\Search\Search;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseListResources extends ListRecords
{
    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Using Laravel Scout to search.
     *
     * @param  class-string<Model>  $modelClass
     */
    protected function makeScout(Builder $query, string $modelClass): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);

        $model = new $modelClass;

        if (filled($search = $this->getTableSearch())) {
            $search = $this->escapeReservedChars($search);

            $keys = Search::search($modelClass, new Criteria($search))
                ->keys();

            $query
                ->whereIn($model->getKeyName(), $keys)
                ->orderByRaw("FIELD({$this->getResource()::getRecordRouteKeyName()}, ".implode(',', $keys).')');
        }

        return $query;
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
