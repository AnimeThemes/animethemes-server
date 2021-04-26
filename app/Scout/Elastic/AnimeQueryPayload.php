<?php

namespace App\Scout\Elastic;

use App\Models\Anime;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;

class AnimeQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    protected function buildQuery()
    {
        return Anime::boolSearch()
            ->should((new MatchPhraseQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
            )
            ->should((new MatchQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
                ->operator('AND')
            )
            ->should((new MatchQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should((new NestedQueryBuilder())
                ->path('synonyms')
                ->query((new MatchPhraseQueryBuilder())
                    ->field('synonyms.text')
                    ->query($this->parser->getSearch())
                )
            )
            ->should((new NestedQueryBuilder())
                ->path('synonyms')
                ->query((new MatchQueryBuilder())
                    ->field('synonyms.text')
                    ->query($this->parser->getSearch())
                    ->operator('AND')
                )
            )
            ->should((new NestedQueryBuilder())
                ->path('synonyms')
                ->query((new MatchQueryBuilder())
                    ->field('synonyms.text')
                    ->query($this->parser->getSearch())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
                )
            )
            ->minimumShouldMatch(1);
    }
}
