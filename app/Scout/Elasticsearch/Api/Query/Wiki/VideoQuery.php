<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class VideoQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                'bool' => [
                    'should' => [
                        [
                            'dis_max' => [
                                'queries' => [
                                    'bool' => [
                                        'should' => $this->createTextQuery('filename', $criteria->getTerm()),
                                    ],
                                ],
                            ],
                        ],
                        [
                            'dis_max' => [
                                'queries' => [
                                    [
                                        'bool' => [
                                            'should' => $this->createTextQuery('tags', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.85,
                                            'should' => $this->createTextQuery('tags_slug', $criteria->getTerm()),
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
                                            'should' => $this->createNestedTextQuery('entries.theme.song', 'title', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.85,
                                            'should' => $this->createNestedTextQuery('entries.theme.song', 'title_native', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5,
                                            'should' => $this->createNestedTextQuery('entries.theme.anime', 'name', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5 * 0.85,
                                            'should' => $this->createNestedTextQuery('entries.theme.anime.synonyms', 'text', $criteria->getTerm()),
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
                                            'should' => $this->createTextQuery('version_slug', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.85,
                                            'should' => $this->createTextQuery('anime_slug', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.85,
                                            'should' => $this->createTextQuery('synonym_slug', $criteria->getTerm()),
                                        ],
                                    ],
                                ],
                            ],
                        ],

                    ],
                ],
            ]);

        return Video::searchQuery($query);
    }
}
