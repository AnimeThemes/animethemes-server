<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki;

use App\Http\Api\Criteria\Search\Criteria;
use App\Models\Wiki\Song;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class SongQuery extends ElasticQuery
{
    public function build(Criteria $criteria): SearchParametersBuilder
    {
        $query = Query::bool()
            ->should(
                new MatchPhraseQueryBuilder()
                    ->field('title')
                    ->query($criteria->getTerm())
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('title')
                    ->query($criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                new MatchQueryBuilder()
                    ->field('title')
                    ->query($criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);

        return Song::searchQuery($query);
    }
}
