<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class StudioQuery extends ElasticQuery
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
                    ],
                ],
            ]);

        return Studio::searchQuery($query);
    }
}
