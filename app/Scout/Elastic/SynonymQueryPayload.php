<?php

declare(strict_types=1);

namespace App\Scout\Elastic;

use App\Models\Wiki\Synonym;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class SynonymQueryPayload.
 */
class SynonymQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    public function buildQuery(): SearchRequestBuilder
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
