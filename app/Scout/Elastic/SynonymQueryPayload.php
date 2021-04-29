<?php

namespace App\Scout\Elastic;

use App\Models\Synonym;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;

class SynonymQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function buildQuery()
    {
        return Synonym::boolSearch()
            ->should((new MatchPhraseQueryBuilder())
                ->field('text')
                ->query($this->parser->getSearch())
            )
            ->should((new MatchQueryBuilder())
                ->field('text')
                ->query($this->parser->getSearch())
                ->operator('AND')
            )
            ->should((new MatchQueryBuilder())
                ->field('text')
                ->query($this->parser->getSearch())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);
    }
}
