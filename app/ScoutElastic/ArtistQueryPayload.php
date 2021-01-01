<?php

namespace App\ScoutElastic;

use App\Http\Resources\ArtistCollection;
use App\Models\Artist;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;

class ArtistQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
    {
        return Artist::boolSearch()
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
                ->path('songs')
                ->query((new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query((new MatchPhraseQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->parser->getSearch())
                    )
                )
            )
            ->should((new NestedQueryBuilder())
                ->path('songs')
                ->query((new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query((new MatchQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->parser->getSearch())
                        ->operator('AND')
                    )
                )
            )
            ->should((new NestedQueryBuilder())
                ->path('songs')
                ->query((new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query((new MatchQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->parser->getSearch())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->minimumShouldMatch(1)
            ->size($this->parser->getLimit())
            ->load($this->parser->getResourceIncludePaths(ArtistCollection::allowedIncludePaths(), ArtistCollection::resourceType()))
            ->execute()
            ->models();
    }
}
