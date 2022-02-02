<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use App\Pivots\AnimeResource as AnimeResourcePivot;

/**
 * Class AnimeSchema.
 */
class AnimeSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Anime::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(ArtistSchema::class, Anime::RELATION_ARTISTS),
            AllowedInclude::make(EntrySchema::class, Anime::RELATION_ENTRIES),
            AllowedInclude::make(ExternalResourceSchema::class, Anime::RELATION_RESOURCES),
            AllowedInclude::make(ImageSchema::class, Anime::RELATION_IMAGES),
            AllowedInclude::make(SeriesSchema::class, Anime::RELATION_SERIES),
            AllowedInclude::make(SongSchema::class, Anime::RELATION_SONG),
            AllowedInclude::make(StudioSchema::class, Anime::RELATION_STUDIOS),
            AllowedInclude::make(SynonymSchema::class, Anime::RELATION_SYNONYMS),
            AllowedInclude::make(ThemeSchema::class, Anime::RELATION_THEMES),
            AllowedInclude::make(VideoSchema::class, Anime::RELATION_VIDEOS),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IntField(BaseResource::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID),
                new StringField(Anime::ATTRIBUTE_NAME),
                new EnumField(Anime::ATTRIBUTE_SEASON, AnimeSeason::class),
                new StringField(Anime::ATTRIBUTE_SLUG),
                new StringField(Anime::ATTRIBUTE_SYNOPSIS),
                new IntField(Anime::ATTRIBUTE_YEAR),
                new StringField(AnimeResourcePivot::ATTRIBUTE_AS, null, Category::COMPUTED()),
            ],
        );
    }
}
