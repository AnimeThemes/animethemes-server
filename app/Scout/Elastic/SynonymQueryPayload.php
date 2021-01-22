<?php

namespace App\Scout\Elastic;

use App\Http\Resources\SynonymCollection;
use App\Models\Synonym;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;

class SynonymQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
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
            ->minimumShouldMatch(1)
            ->size($this->parser->getLimit())
            ->load($this->parser->getResourceIncludePaths(SynonymCollection::allowedIncludePaths(), SynonymCollection::resourceType()))
            ->execute()
            ->models();
    }
}
