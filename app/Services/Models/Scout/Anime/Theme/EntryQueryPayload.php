<?php

declare(strict_types=1);

namespace App\Services\Models\Scout\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Services\Models\Scout\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class EntryQueryPayload.
 */
class EntryQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model = AnimeThemeEntry::class;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder|BoolQueryBuilder
     */
    public function buildQuery(): SearchRequestBuilder | BoolQueryBuilder
    {
        return AnimeThemeEntry::boolSearch()
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('version')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('version')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('version')
                ->query($this->criteria->getTerm())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->should(
                (new MatchPhraseQueryBuilder())
                ->field('version_slug')
                ->query($this->criteria->getTerm())
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('version_slug')
                ->query($this->criteria->getTerm())
                ->operator('AND')
            )
            ->should(
                (new MatchQueryBuilder())
                ->field('version_slug')
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
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new MatchPhraseQueryBuilder())
                        ->field('theme.anime.name')
                        ->query($this->criteria->getTerm())
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('theme.anime.name')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('theme.anime.name')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new NestedQueryBuilder())
                        ->path('theme.anime.synonyms')
                        ->query(
                            (new MatchPhraseQueryBuilder())
                            ->field('theme.anime.synonyms.text')
                            ->query($this->criteria->getTerm())
                        )
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new NestedQueryBuilder())
                        ->path('theme.anime.synonyms')
                        ->query(
                            (new MatchQueryBuilder())
                            ->field('theme.anime.synonyms.text')
                            ->query($this->criteria->getTerm())
                            ->operator('AND')
                        )
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.anime')
                    ->query(
                        (new NestedQueryBuilder())
                        ->path('theme.anime.synonyms')
                        ->query(
                            (new MatchQueryBuilder())
                            ->field('theme.anime.synonyms.text')
                            ->query($this->criteria->getTerm())
                            ->operator('AND')
                            ->lenient(true)
                            ->fuzziness('AUTO')
                        )
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.song')
                    ->query(
                        (new MatchPhraseQueryBuilder())
                        ->field('theme.song.title')
                        ->query($this->criteria->getTerm())
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.song')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('theme.song.title')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                    )
                )
            )
            ->should(
                (new NestedQueryBuilder())
                ->path('theme')
                ->query(
                    (new NestedQueryBuilder())
                    ->path('theme.song')
                    ->query(
                        (new MatchQueryBuilder())
                        ->field('theme.song.title')
                        ->query($this->criteria->getTerm())
                        ->operator('AND')
                        ->lenient(true)
                        ->fuzziness('AUTO')
                    )
                )
            )
            ->minimumShouldMatch(1);
    }
}
