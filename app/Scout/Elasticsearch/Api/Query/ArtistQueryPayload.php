<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\ArtistSchema;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

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
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new ArtistSchema();
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchParametersBuilder
     */
    public function buildQuery(): SearchParametersBuilder
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
