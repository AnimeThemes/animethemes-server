<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Series;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class SeriesQuery extends ElasticQuery
{
    /**
     * Build Elasticsearch query.
     */
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                'dis_max' => [
                    'queries' => [
                        [
                            'bool' => [
                                'should' => $this->createTextQuery('name', $criteria->getTerm()),
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.6,
                                'should' => $this->createNestedQuery(
                                    'anime',
                                    $this->createNestedTextQuery('anime.synonyms', 'text', $criteria->getTerm())
                                ),
                            ],
                        ],
                    ],
                ],
            ]);

        return Series::searchQuery($query);
    }
}
