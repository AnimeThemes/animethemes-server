<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\AnimeSchema;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class AnimeQueryPayload.
 */
class AnimeQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Anime::class;
    }

    /**
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new AnimeSchema();
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
                                                        'query' => $this->criteria->getTerm(),
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
                                                        'query' => $this->criteria->getTerm(),
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
                                                        'query' => $this->criteria->getTerm(),
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
                    ],
                ],
            ]);

        return Anime::searchQuery($query);
    }
}
