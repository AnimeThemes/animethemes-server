<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Models\Wiki\Artist;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Support\Query;

/**
 * Class ArtistQueryPayload.
 */
class ArtistQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Artist::class;
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    public function buildQuery(): SearchRequestBuilder
    {
        $query = Query::bool()
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('name')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('name')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('name')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('songs')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query(
                        (new MatchPhraseQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->criteria->getTerm())
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('songs')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('songs')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('songs.pivot')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('songs.pivot.as')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->minimumShouldMatch(1);

        return Artist::searchQuery($query);
    }
}
