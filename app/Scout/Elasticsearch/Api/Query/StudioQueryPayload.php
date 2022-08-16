<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\StudioSchema;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

/**
 * Class StudioQueryPayload.
 */
class StudioQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return Studio::class;
    }

    /**
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new StudioSchema();
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
            ->minimumShouldMatch(1);

        return Studio::searchQuery($query);
    }
}
