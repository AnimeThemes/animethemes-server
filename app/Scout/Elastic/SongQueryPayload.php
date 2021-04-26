<?php

namespace App\Scout\Elastic;

use App\Models\Song;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;

class SongQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    protected function buildQuery()
    {
        return Song::boolSearch()
            ->should((new MatchPhraseQueryBuilder())
                ->field('title')
                ->query($this->parser->getSearch())
            )
            ->should((new MatchQueryBuilder())
                ->field('title')
                ->query($this->parser->getSearch())
                ->operator('AND')
            )
            ->should((new MatchQueryBuilder())
                ->field('title')
                ->query($this->parser->getSearch())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);
    }
}
