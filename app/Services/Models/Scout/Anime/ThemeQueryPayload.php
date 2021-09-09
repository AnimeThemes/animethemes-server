<?php

declare(strict_types=1);

namespace App\Services\Models\Scout\Anime;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Services\Models\Scout\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class ThemeQueryPayload.
 */
class ThemeQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model = AnimeTheme::class;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder|BoolQueryBuilder
     */
    public function buildQuery(): SearchRequestBuilder|BoolQueryBuilder
    {
        return AnimeTheme::boolSearch()
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
    }
}
