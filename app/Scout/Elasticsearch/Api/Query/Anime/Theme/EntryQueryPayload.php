<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query\Anime\Theme;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Support\Query;

/**
 * Class EntryQueryPayload.
 */
class EntryQueryPayload extends ElasticQueryPayload
{
    /**
     * The model this payload is searching.
     *
     * @return string
     */
    public static function model(): string
    {
        return AnimeThemeEntry::class;
    }

    /**
     * The schema this payload is searching.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new EntrySchema();
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

        return AnimeThemeEntry::searchQuery($query);
    }
}
