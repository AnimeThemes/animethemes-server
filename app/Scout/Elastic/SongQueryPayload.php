<?php

namespace App\Scout\Elastic;

use App\Http\Resources\SongCollection;
use App\Models\Song;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use Illuminate\Support\Str;

class SongQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function doPerformSearch()
    {
        $builder = Song::boolSearch()
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
            ->load($this->parser->getResourceIncludePaths(SongCollection::allowedIncludePaths(), Str::lower(SongCollection::$wrap)));

        return $builder->execute()->models();
    }
}
