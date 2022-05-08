<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Search\SearchAnimeField;
use App\Http\Api\Field\Wiki\Search\SearchArtistField;
use App\Http\Api\Field\Wiki\Search\SearchSeriesField;
use App\Http\Api\Field\Wiki\Search\SearchSongField;
use App\Http\Api\Field\Wiki\Search\SearchStudioField;
use App\Http\Api\Field\Wiki\Search\SearchThemeField;
use App\Http\Api\Field\Wiki\Search\SearchVideoField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\SearchResource;

/**
 * Class SearchSchema.
 */
class SearchSchema extends Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SearchResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(AnimeSchema::class, ''),
            new AllowedInclude(ThemeSchema::class, ''),
            new AllowedInclude(ArtistSchema::class, ''),
            new AllowedInclude(SeriesSchema::class, ''),
            new AllowedInclude(SongSchema::class, ''),
            new AllowedInclude(StudioSchema::class, ''),
            new AllowedInclude(VideoSchema::class, ''),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new SearchAnimeField(),
            new SearchThemeField(),
            new SearchArtistField(),
            new SearchSeriesField(),
            new SearchSongField(),
            new SearchStudioField(),
            new SearchVideoField(),
        ];
    }
}
