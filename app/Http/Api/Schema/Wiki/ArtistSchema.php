<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistResource as ArtistResourcePivot;

/**
 * Class ArtistSchema.
 */
class ArtistSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Artist::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Artist::RELATION_ANIME),
            AllowedInclude::make(ArtistSchema::class, Artist::RELATION_GROUPS),
            AllowedInclude::make(ArtistSchema::class, Artist::RELATION_MEMBERS),
            AllowedInclude::make(ExternalResourceSchema::class, Artist::RELATION_RESOURCES),
            AllowedInclude::make(ImageSchema::class, Artist::RELATION_IMAGES),
            AllowedInclude::make(SongSchema::class, Artist::RELATION_SONGS),
            AllowedInclude::make(ThemeSchema::class, Artist::RELATION_ANIMETHEMES),
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
                new IntField(BaseResource::ATTRIBUTE_ID, Artist::ATTRIBUTE_ID),
                new StringField(Artist::ATTRIBUTE_NAME),
                new StringField(Artist::ATTRIBUTE_SLUG),
                new StringField(ArtistResourcePivot::ATTRIBUTE_AS, null, Category::COMPUTED()),
            ],
        );
    }
}
