<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Sort;

use App\Http\Api\Sort\Sort;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Support\Str;

/**
 * Class RelationCriteria.
 */
class RelationCriteria extends FieldCriteria
{
    /**
     * Apply criteria to builder.
     *
     * @param  SearchRequestBuilder  $builder
     * @param  Sort  $sort
     * @return SearchRequestBuilder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function sort(SearchRequestBuilder $builder, Sort $sort): SearchRequestBuilder
    {
        return $builder->sortRaw([
            [
                $sort->getColumn() => [
                    'order' => $this->direction->value,
                    'nested' => [
                        'path' => Str::beforeLast($sort->getColumn(), '.'),
                    ],
                ],
            ],
        ]);
    }
}
