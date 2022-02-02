<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime\Theme;

use App\Http\Api\Field\BooleanField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class EntrySchema.
 */
class EntrySchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = AnimeThemeEntry::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return EntryResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, AnimeThemeEntry::RELATION_ANIME),
            AllowedInclude::make(ThemeSchema::class, AnimeThemeEntry::RELATION_THEME),
            AllowedInclude::make(VideoSchema::class, AnimeThemeEntry::RELATION_VIDEOS),
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
                new IntField(BaseResource::ATTRIBUTE_ID, AnimeThemeEntry::ATTRIBUTE_ID),
                new StringField(AnimeThemeEntry::ATTRIBUTE_EPISODES),
                new StringField(AnimeThemeEntry::ATTRIBUTE_NOTES),
                new BooleanField(AnimeThemeEntry::ATTRIBUTE_NSFW),
                new BooleanField(AnimeThemeEntry::ATTRIBUTE_SPOILER),
                new IntField(AnimeThemeEntry::ATTRIBUTE_VERSION),
            ],
        );
    }
}
