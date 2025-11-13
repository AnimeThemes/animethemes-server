<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\List;

use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class PlaylistQuery extends ElasticQuery
{
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
                    ],
                ],
            ]);

        return Playlist::searchQuery($query);
    }
}
