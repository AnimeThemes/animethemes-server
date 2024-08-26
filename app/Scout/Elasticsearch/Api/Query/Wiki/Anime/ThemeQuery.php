<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class ThemeQuery.
 */
class ThemeQuery extends ElasticQuery
{
    /**
     * Build Elasticsearch query.
     *
     * @param  Criteria  $criteria
     * @return SearchParametersBuilder
     */
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
                                            'should' => $this->createNestedTextQuery('song', 'title', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5,
                                            'should' => $this->createNestedTextQuery('anime', 'name', $criteria->getTerm()),
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'boost' => 0.5 * 0.85,
                                            'should' => $this->createNestedQuery(
                                                'anime',
                                                $this->createNestedTextQuery('anime.synonyms', 'text', $criteria->getTerm()),
                                            ),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'boost' => 0.75,
                                'should' => $this->createTextQuery('slug', $criteria->getTerm()),
                            ],
                        ]
                    ],
                ],
            ]);

        return AnimeTheme::searchQuery($query);
    }
}
