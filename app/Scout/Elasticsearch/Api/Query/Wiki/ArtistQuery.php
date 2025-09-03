<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class ArtistQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('name')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('name')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('name')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchPhraseQueryBuilder()
                            ->field('performances.alias')
                            ->query($criteria->getTerm())
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.alias')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->boost(0.8 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.alias')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->lenient(true)
                            ->fuzziness('AUTO')
                            ->boost(0.8 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchPhraseQueryBuilder()
                            ->field('performances.as')
                            ->query($criteria->getTerm())
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.as')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->boost(0.5 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.as')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->lenient(true)
                            ->fuzziness('AUTO')
                            ->boost(0.5 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchPhraseQueryBuilder()
                            ->field('performances.membership_alias')
                            ->query($criteria->getTerm())
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.membership_alias')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->boost(0.8 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.membership_alias')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->lenient(true)
                            ->fuzziness('AUTO')
                            ->boost(0.8 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchPhraseQueryBuilder()
                            ->field('performances.membership_as')
                            ->query($criteria->getTerm())
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.membership_as')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->boost(0.5 * 0.85)
                    )
            )
            ->should(
                new NestedQueryBuilder()
                    ->path('performances')
                    ->query(
                        new MatchQueryBuilder()
                            ->field('performances.membership_as')
                            ->query($criteria->getTerm())
                            ->operator('AND')
                            ->lenient(true)
                            ->fuzziness('AUTO')
                            ->boost(0.5 * 0.85)
                    )
            )
            ->minimumShouldMatch(1);

        return Artist::searchQuery($query);
    }
}
