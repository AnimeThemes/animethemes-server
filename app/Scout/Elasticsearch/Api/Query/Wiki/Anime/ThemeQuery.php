<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
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
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('slug')
                ->query($criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('slug')
                ->query($criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('slug')
                ->query($criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('anime_slug')
                ->query($criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('anime_slug')
                ->query($criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('anime_slug')
                ->query($criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('synonym_slug')
                ->query($criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('synonym_slug')
                ->query($criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('synonym_slug')
                ->query($criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchPhraseQueryBuilder())
                    ->field('anime.name')
                    ->query($criteria->getTerm())
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('anime.name')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('anime.name')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchPhraseQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($criteria->getTerm())
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchPhraseQueryBuilder())
                    ->field('song.title')
                    ->query($criteria->getTerm())
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('song.title')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('song.title')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
                )
            )
            ->minimumShouldMatch(1);

        return AnimeTheme::searchQuery($query);
    }
}
