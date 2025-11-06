<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class EntryQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->mustRaw([
                // The more sub-queries match the better the score will be.
                'bool' => [
                    'should' => [
                        [
                            // Pick the score of the best performing sub-query.
                            'dis_max' => [
                                'queries' => [
                                    [
                                        'bool' => [
                                            'should' => $this->createNestedTextQuery('theme.song', 'title', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.85,
                                            'should' => $this->createNestedTextQuery('theme.song', 'title_native', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5,
                                            'should' => $this->createNestedTextQuery('theme.anime', 'name', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5 * 0.85,
                                            'should' => $this->createNestedTextQuery('theme.anime.synonyms', 'text', $criteria->getTerm()),
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

        return AnimeThemeEntry::searchQuery($query);
    }
}
