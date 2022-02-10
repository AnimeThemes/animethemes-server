<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Query;

use App\Models\Wiki\Studio;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Support\Query;

/**
 * Class StudioQueryPayload.
 */
class StudioQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Studio::class;
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    public function buildQuery(): SearchRequestBuilder
    {
        $query = Query::bool()
            ->should(
                (new MatchPhraseQueryBuilder())
                    ->field('name')
                    ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                    ->field('name')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                    ->field('name')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);

        return Studio::searchQuery($query);
    }
}
