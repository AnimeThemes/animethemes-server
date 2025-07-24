<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class AnimeQuery extends ElasticQuery
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
                                'should' => $this->createTextQuery('name', $criteria->getTerm()),
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.85,
                                'should' => $this->createNestedTextQuery('synonyms', 'text', $criteria->getTerm()),
                            ],
                        ],
                    ],
                ],
            ]);

        return Anime::searchQuery($query);
    }
}
