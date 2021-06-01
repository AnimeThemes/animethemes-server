<?php declare(strict_types=1);

namespace App\Scout\Elastic;

use App\Models\Artist;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class ArtistQueryPayload
 * @package App\Scout\Elastic
 */
class ArtistQueryPayload extends ElasticQueryPayload
{
    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    public function buildQuery(): SearchRequestBuilder
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
            ->minimumShouldMatch(1);
    }
}
