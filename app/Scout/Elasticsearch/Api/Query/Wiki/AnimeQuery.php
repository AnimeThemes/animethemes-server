<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class AnimeQuery.
 */
class AnimeQuery extends ElasticQuery
{
    /**
     * Build Elasticsearch query.
     *
     * @param  Criteria  $criteria
     * @return SearchParametersBuilder
     */
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                // Pick the score of the best performing sub-query.
                'dis_max' => [
                    'queries' => [
                        [
                            // The more sub-queries match the better the score will be.
                            'bool' => [
                                'should' => [
                                    [
                                        'match_phrase' => [
                                            'name' => [
                                                'query' => $criteria->getTerm(),
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'name' => [
                                                'query' => $criteria->getTerm(),
                                                'operator' => 'AND',
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'name' => [
                                                'query' => $criteria->getTerm(),
                                                'boost' => 0.6,
                                            ],
                                        ],
                                    ],
                                    [
                                        'fuzzy' => [
                                            'name' => [
                                                'value' => $criteria->getTerm(),
                                                'boost' => 0.4,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.85,
                                'should' => [
                                    [
                                        'nested' => [
                                            'path' => 'synonyms',
                                            'query' => [
                                                'match_phrase' => [
                                                    'synonyms.text' => [
                                                        'query' => $criteria->getTerm(),
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'synonyms',
                                            'query' => [
                                                'match' => [
                                                    'synonyms.text' => [
                                                        'query' => $criteria->getTerm(),
                                                        'operator' => 'AND',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'synonyms',
                                            'query' => [
                                                'match' => [
                                                    'synonyms.text' => [
                                                        'query' => $criteria->getTerm(),
                                                        'boost' => 0.6,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'synonyms',
                                            'query' => [
                                                'fuzzy' => [
                                                    'synonyms.text' => [
                                                        'value' => $criteria->getTerm(),
                                                        'boost' => 0.4,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        return Anime::searchQuery($query);
    }
}
