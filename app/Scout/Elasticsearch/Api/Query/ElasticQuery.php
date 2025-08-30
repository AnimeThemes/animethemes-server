<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Http\Api\Criteria\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;

abstract class ElasticQuery
{
    abstract public function build(Criteria $criteria): SearchParametersBuilder;

    /**
     * Helper function for raw queries. This will create four queries against a text field:
     * - Matching the exact phrase (x1.0)
     * - Matching all terms (words) (x1.0)
     * - Matching at least one term (word) (x0.6)
     * - Matching fuzzy (x0.4)
     *
     * @return array
     */
    protected function createTextQuery(string $field, string $searchTerm): array
    {
        return [
            [
                'match_phrase' => [
                    $field => [
                        'query' => $searchTerm,
                    ],
                ],
            ],
            [
                'match' => [
                    $field => [
                        'query' => $searchTerm,
                        'operator' => 'AND',
                    ],
                ],
            ],
            [
                'match' => [
                    $field => [
                        'query' => $searchTerm,
                        'boost' => 0.6,
                    ],
                ],
            ],
            [
                'fuzzy' => [
                    $field => [
                        'value' => $searchTerm,
                        'boost' => 0.4,
                    ],
                ],
            ],
        ];
    }

    /**
     * Helper function for raw queries. This will wrap queries in nested queries.
     *
     * @param  array  $nestedQueries
     * @return array
     */
    protected function createNestedQuery(string $nestedResource, array $nestedQueries): array
    {
        return array_map(
            fn (array $nestedQuery) => [
                'nested' => [
                    'path' => $nestedResource,
                    'query' => $nestedQuery,
                ],
            ],
            $nestedQueries
        );
    }

    /**
     * Shorthand function for calling `$this->createNestedQuery()` with the output from
     * `$this->createTextQuery()`.
     *
     * @return array
     */
    protected function createNestedTextQuery(string $nestedResource, string $field, string $searchTerm): array
    {
        return $this->createNestedQuery($nestedResource, $this->createTextQuery("$nestedResource.$field", $searchTerm));
    }
}
