<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class EntryQuery.
 */
class EntryQuery extends ElasticQuery
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
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('version')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('version')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('version')
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
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new MatchPhraseQueryBuilder()
                                    ->field('theme.anime.name')
                                    ->query($criteria->getTerm())
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new MatchQueryBuilder()
                                    ->field('theme.anime.name')
                                    ->query($criteria->getTerm())
                                    ->operator('AND')
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new MatchQueryBuilder()
                                    ->field('theme.anime.name')
                                    ->query($criteria->getTerm())
                                    ->operator('AND')
                                    ->lenient(true)
                                    ->fuzziness('AUTO')
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('theme.anime.synonyms')
                                    ->query(
                                        new MatchPhraseQueryBuilder()
                                            ->field('theme.anime.synonyms.text')
                                            ->query($criteria->getTerm())
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('theme.anime.synonyms')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('theme.anime.synonyms.text')
                                            ->query($criteria->getTerm())
                                            ->operator('AND')
                                    )
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.anime')
                            ->query(
                                new NestedQueryBuilder()
                                    ->path('theme.anime.synonyms')
                                    ->query(
                                        new MatchQueryBuilder()
                                            ->field('theme.anime.synonyms.text')
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
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.song')
                            ->query(
                                new MatchPhraseQueryBuilder()
                                    ->field('theme.song.title')
                                    ->query($criteria->getTerm())
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.song')
                            ->query(
                                new MatchQueryBuilder()
                                    ->field('theme.song.title')
                                    ->query($criteria->getTerm())
                                    ->operator('AND')
                            )
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('theme')
                    ->query(
                        new NestedQueryBuilder()
                            ->path('theme.song')
                            ->query(
                                new MatchQueryBuilder()
                                    ->field('theme.song.title')
                                    ->query($criteria->getTerm())
                                    ->operator('AND')
                                    ->lenient(true)
                                    ->fuzziness('AUTO')
                            )
                    )
            )
            ->minimumShouldMatch(1);

        return AnimeThemeEntry::searchQuery($query);
    }
}
