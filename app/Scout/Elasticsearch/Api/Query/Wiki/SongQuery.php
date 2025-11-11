<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Models\Wiki\Song;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class SongQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                'dis_max' => [
                    'queries' => [
                        [
                            'bool' => [
                                'should' => $this->createTextQuery('title', $criteria->getTerm()),
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.85,
                                'should' => $this->createTextQuery('title_native', $criteria->getTerm()),
                            ],
                        ],
                    ],
                ],
            ]);

        return Song::searchQuery($query);
    }
}
