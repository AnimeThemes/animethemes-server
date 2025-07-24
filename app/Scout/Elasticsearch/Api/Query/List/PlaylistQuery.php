<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\List;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class PlaylistQuery extends ElasticQuery
{
    /**
     * Build Elasticsearch query.
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
                    ],
                ],
            ]);

        return Playlist::searchQuery($query);
    }
}
