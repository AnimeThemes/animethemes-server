<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
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
            AllowedInclude::make(AnimeSchema::class, ''),
            AllowedInclude::make(ThemeSchema::class, ''),
            AllowedInclude::make(ArtistSchema::class, ''),
            AllowedInclude::make(SeriesSchema::class, ''),
            AllowedInclude::make(SongSchema::class, ''),
            AllowedInclude::make(StudioSchema::class, ''),
            AllowedInclude::make(VideoSchema::class, ''),
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
            new StringField(AnimeCollection::$wrap, null, Category::COMPUTED()),
            new StringField(ThemeCollection::$wrap, null, Category::COMPUTED()),
            new StringField(ArtistCollection::$wrap, null, Category::COMPUTED()),
            new StringField(SeriesCollection::$wrap, null, Category::COMPUTED()),
            new StringField(SongCollection::$wrap, null, Category::COMPUTED()),
            new StringField(StudioCollection::$wrap, null, Category::COMPUTED()),
            new StringField(VideoCollection::$wrap, null, Category::COMPUTED()),
        ];
    }
}
