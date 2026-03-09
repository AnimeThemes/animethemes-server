<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class ThemeQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                // The more sub-queries match the better the score will be.
                'bool' => [
                    'should' => [
                        [
                            'dis_max' => [
                                'queries' => [
                                    [
                                        'bool' => [
                                            'should' => $this->createNestedTextQuery('song', 'title', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.9,
                                            'should' => $this->createNestedTextQuery('song', 'title_native', $criteria->getTerm()),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            // Pick the score of the best performing sub-query.
                            'dis_max' => [
                                'queries' => [
                                    [
                                        'bool' => [
                                            'boost' => 0.8,
                                            'should' => $this->createNestedTextQuery('anime', 'name', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.8 * 0.85,
                                            'should' => $this->createNestedTextQuery('anime', 'synonyms', $criteria->getTerm()),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'dis_max' => [
                                'queries' => [
                                    [
                                        'bool' => [
                                            'should' => $this->createTextQuery('slug', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'should' => $this->createTextQuery('anime_slug', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.7,
                                            'should' => $this->createTextQuery('synonym_slug', $criteria->getTerm()),
                                        ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        return AnimeTheme::searchQuery($query);
    }
}
