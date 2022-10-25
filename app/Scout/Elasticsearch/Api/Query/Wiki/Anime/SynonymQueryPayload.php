<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\SynonymSchema;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class SynonymQueryPayload.
 */
class SynonymQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return AnimeSynonym::class;
    }

    /**
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new SynonymSchema();
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
                ->field('text')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('text')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('text')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1);

        return AnimeSynonym::searchQuery($query);
    }
}
