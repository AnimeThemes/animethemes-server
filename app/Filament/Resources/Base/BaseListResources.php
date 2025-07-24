<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\Actions\Base\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseListResources extends ListRecords
{
    /**
     * Get the header actions available.
     *
     * @return array<int, \Filament\Actions\Action>
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     * @param  Builder  $query
     * @param  class-string<Model>  $modelClass
     * @return Builder
     */
    protected function makeScout(Builder $query, string $modelClass): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);

        $model = new $modelClass;

        if (filled($search = $this->getTableSearch())) {
            $search = $this->escapeReservedChars($search);
            /** @phpstan-ignore-next-line */
            $keys = $modelClass::search($search)->take(25)->keys();

            $query
                ->whereIn($model->getKeyName(), $keys)
                ->orderByRaw("FIELD({$this->getResource()::getRecordRouteKeyName()}, ".$keys->implode(',').')');
        }

        return $query;
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
