<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Models\Wiki\Synonym;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class SynonymQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                'dis_max' => [
                    'queries' => [
                        [
                            'bool' => [
                                'should' => $this->createTextQuery('text', $criteria->getTerm()),
                            ],
                        ],
                    ],
                ],
            ]);

        return Synonym::searchQuery($query);
    }
}
