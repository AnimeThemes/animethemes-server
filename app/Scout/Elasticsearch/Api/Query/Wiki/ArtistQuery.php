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

/**
 * Class ArtistQuery.
 */
class ArtistQuery extends ElasticQuery
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
                ->path('songs')
                ->query(
                    new NestedQueryBuilder()
                    ->path('songs.pivot')
                    ->query(
                        new MatchPhraseQueryBuilder()
                        ->field('songs.pivot.as')
                        ->query($criteria->getTerm())
                    )
                )
            )
            ->should(
                new NestedQueryBuilder()
                ->path('songs')
                ->query(
                    new NestedQueryBuilder()
                    ->path('songs.pivot')
                    ->query(
                        new MatchQueryBuilder()
                        ->field('songs.pivot.as')
                        ->query($criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                new NestedQueryBuilder()
                ->path('songs')
                ->query(
                    new NestedQueryBuilder()
                    ->path('songs.pivot')
                    ->query(
                        new MatchQueryBuilder()
                        ->field('songs.pivot.as')
                        ->query($criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->minimumShouldMatch(1);

        return Artist::searchQuery($query);
    }
}
