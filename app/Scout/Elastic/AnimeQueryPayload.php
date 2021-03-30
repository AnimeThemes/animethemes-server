<?php

namespace App\Scout\Elastic;

use App\Http\Resources\AnimeCollection;
use App\Models\Anime;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use Illuminate\Support\Str;

class AnimeQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function doPerformSearch()
    {
        $builder = Anime::boolSearch()
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
            ->minimumShouldMatch(1)
            ->size($this->parser->getLimit())
            ->load($this->parser->getResourceIncludePaths(AnimeCollection::allowedIncludePaths(), Str::singular(AnimeCollection::$wrap)));

        return $builder->execute()->models();
    }
}
