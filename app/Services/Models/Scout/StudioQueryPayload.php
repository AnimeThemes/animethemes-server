<?php

declare(strict_types=1);

namespace App\Services\Models\Scout;

use App\Models\Wiki\Studio;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class StudioQueryPayload.
 */
class StudioQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model = Studio::class;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder|BoolQueryBuilder
     */
    public function buildQuery(): SearchRequestBuilder | BoolQueryBuilder
    {
        return Studio::boolSearch()
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
    }
}
