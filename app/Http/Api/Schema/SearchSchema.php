<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\Search\SearchAnimeField;
use App\Http\Api\Field\Search\SearchArtistField;
use App\Http\Api\Field\Search\SearchPlaylistField;
use App\Http\Api\Field\Search\SearchSeriesField;
use App\Http\Api\Field\Search\SearchSongField;
use App\Http\Api\Field\Search\SearchStudioField;
use App\Http\Api\Field\Search\SearchThemeField;
use App\Http\Api\Field\Search\SearchVideoField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\SearchResource;

class SearchSchema extends Schema
{
    public function type(): string
    {
        return SearchResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), ''),
            new AllowedInclude(new ThemeSchema(), ''),
            new AllowedInclude(new ArtistSchema(), ''),
            new AllowedInclude(new PlaylistSchema(), ''),
            new AllowedInclude(new SeriesSchema(), ''),
            new AllowedInclude(new SongSchema(), ''),
            new AllowedInclude(new StudioSchema(), ''),
            new AllowedInclude(new VideoSchema(), ''),
        ];
    }

    /**
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new SearchAnimeField($this),
            new SearchThemeField($this),
            new SearchArtistField($this),
            new SearchPlaylistField($this),
            new SearchSeriesField($this),
            new SearchSongField($this),
            new SearchStudioField($this),
            new SearchVideoField($this),
        ];
    }
}
