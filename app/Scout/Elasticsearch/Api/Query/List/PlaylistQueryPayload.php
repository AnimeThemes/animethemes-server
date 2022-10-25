<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\List;

use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use App\Scout\Elasticsearch\Api\Schema\List\PlaylistSchema;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class PlaylistQueryPayload.
 */
class PlaylistQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Playlist::class;
    }

    /**
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new PlaylistSchema();
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchParametersBuilder
     */
    public function buildQuery(): SearchParametersBuilder
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
                                                'query' => $this->criteria->getTerm(),
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'name' => [
                                                'query' => $this->criteria->getTerm(),
                                                'operator' => 'AND',
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'name' => [
                                                'query' => $this->criteria->getTerm(),
                                                'boost' => 0.6,
                                            ],
                                        ],
                                    ],
                                    [
                                        'fuzzy' => [
                                            'name' => [
                                                'value' => $this->criteria->getTerm(),
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
