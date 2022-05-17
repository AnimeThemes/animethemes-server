<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Models\Wiki\Song;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Support\Query;

/**
 * Class SongQueryPayload.
 */
class SongQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Song::class;
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
                ->field('title')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('title')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('title')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);

        return Song::searchQuery($query);
    }
}
