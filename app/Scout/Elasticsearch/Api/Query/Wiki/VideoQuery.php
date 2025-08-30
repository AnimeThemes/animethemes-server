<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class VideoQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('filename')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('filename')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('filename')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('tags')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('tags')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('tags')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('tags_slug')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('tags_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('tags_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('version_slug')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('version_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('version_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('anime_slug')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('anime_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('anime_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('synonym_slug')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('synonym_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('synonym_slug')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new MatchPhraseQueryBuilder()
                                            ->field('entries.theme.anime.name')
                                            ->query($criteria->getTerm())
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('entries.theme.anime.name')
                                            ->query($criteria->getTerm())
                                            ->operator('AND')
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('entries.theme.anime.name')
                                            ->query($criteria->getTerm())
                                            ->operator('AND')
                                            ->lenient(true)
                                            ->fuzziness('AUTO')
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new NestedQueryBuilder()
                                            ->path('entries.theme.anime.synonyms')
                                            ->query(
                                                new MatchPhraseQueryBuilder()
                                                    ->field('entries.theme.anime.synonyms.text')
                                                    ->query($criteria->getTerm())
                                            )
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new NestedQueryBuilder()
                                            ->path('entries.theme.anime.synonyms')
                                            ->query(
                                                new MatchQueryBuilder()
                                                    ->field('entries.theme.anime.synonyms.text')
                                                    ->query($criteria->getTerm())
                                                    ->operator('AND')
                                            )
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.anime')
                                    ->query(
                                        new NestedQueryBuilder()
                                            ->path('entries.theme.anime.synonyms')
                                            ->query(
                                                new MatchQueryBuilder()
                                                    ->field('entries.theme.anime.synonyms.text')
                                                    ->query($criteria->getTerm())
                                                    ->operator('AND')
                                                    ->lenient(true)
                                                    ->fuzziness('AUTO')
                                            )
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.song')
                                    ->query(
                                        new MatchPhraseQueryBuilder()
                                            ->field('entries.theme.song.title')
                                            ->query($criteria->getTerm())
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.song')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('entries.theme.song.title')
                                            ->query($criteria->getTerm())
                                            ->operator('AND')
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('entries')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('entries.theme')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('entries.theme.song')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('entries.theme.song.title')
                                            ->query($criteria->getTerm())
                                            ->operator('AND')
                                            ->lenient(true)
                                            ->fuzziness('AUTO')
                                    )
                            )
                    )
            )
            ->minimumShouldMatch(1);

        return Video::searchQuery($query);
    }
}
