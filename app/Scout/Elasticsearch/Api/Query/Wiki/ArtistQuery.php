<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Models\Wiki\Artist;
use App\Scout\Criteria;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class ArtistQuery extends ElasticQuery
{
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
                                'boost' => 0.8,
                                'should' => $this->createTextQuery('synonyms', $criteria->getTerm()),
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.8 * 0.85,
                                'should' => $this->createTextQuery('as', $criteria->getTerm()),
                            ],
                        ],
                    ],
                ],
            ]);

        return Artist::searchQuery($query);
    }
}
