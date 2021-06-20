<?php

declare(strict_types=1);

namespace App\Scout\Elastic;

use App\Models\Wiki\Anime;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class AnimeQueryPayload.
 */
class AnimeQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    public function buildQuery(): SearchRequestBuilder
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
