<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Anime;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Support\Query;

/**
 * Class ThemeQueryPayload.
 */
class ThemeQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return AnimeTheme::class;
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
                ->field('slug')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('anime_slug')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('anime_slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('anime_slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('synonym_slug')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('synonym_slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('synonym_slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchPhraseQueryBuilder())
                    ->field('anime.name')
                    ->query($this->criteria->getTerm())
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('anime.name')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('anime.name')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchPhraseQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($this->criteria->getTerm())
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('anime')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('anime.synonyms')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('anime.synonyms.text')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchPhraseQueryBuilder())
                    ->field('song.title')
                    ->query($this->criteria->getTerm())
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('song.title')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('song')
                ->query(
                    (new MatchQueryBuilder())
                    ->field('song.title')
                    ->query($this->criteria->getTerm())
                    ->operator('AND')
                    ->lenient(true)
                    ->fuzziness('AUTO')
                )
            )
            ->minimumShouldMatch(1);

        return AnimeTheme::searchQuery($query);
    }
}
