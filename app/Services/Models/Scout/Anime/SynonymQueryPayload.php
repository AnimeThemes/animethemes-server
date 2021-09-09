<?php

declare(strict_types=1);

namespace App\Services\Models\Scout\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Services\Models\Scout\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class SynonymQueryPayload.
 */
class SynonymQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model = AnimeSynonym::class;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder|BoolQueryBuilder
     */
    public function buildQuery(): SearchRequestBuilder|BoolQueryBuilder
    {
        return AnimeSynonym::boolSearch()
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
    }
}
