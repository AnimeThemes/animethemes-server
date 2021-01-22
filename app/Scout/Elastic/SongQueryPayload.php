<?php

namespace App\Scout\Elastic;

use App\Http\Resources\SongCollection;
use App\Models\Song;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;

class SongQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
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
            ->minimumShouldMatch(1)
            ->size($this->parser->getLimit())
            ->load($this->parser->getResourceIncludePaths(SongCollection::allowedIncludePaths(), SongCollection::resourceType()))
            ->execute()
            ->models();
    }
}
