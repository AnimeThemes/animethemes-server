<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\BooleanField;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;

/**
 * Class VideoSchema.
 */
class VideoSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Video::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return VideoResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Video::RELATION_ANIME),
            AllowedInclude::make(EntrySchema::class, Video::RELATION_ANIMETHEMEENTRIES),
            AllowedInclude::make(ThemeSchema::class, Video::RELATION_ANIMETHEME),
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
                new IntField(BaseResource::ATTRIBUTE_ID, Video::ATTRIBUTE_ID),
                new StringField(Video::ATTRIBUTE_BASENAME),
                new StringField(Video::ATTRIBUTE_FILENAME),
                new BooleanField(Video::ATTRIBUTE_LYRICS),
                new StringField(Video::ATTRIBUTE_MIMETYPE),
                new BooleanField(Video::ATTRIBUTE_NC),
                new EnumField(Video::ATTRIBUTE_OVERLAP, VideoOverlap::class),
                new StringField(Video::ATTRIBUTE_PATH),
                new IntField(Video::ATTRIBUTE_RESOLUTION),
                new IntField(Video::ATTRIBUTE_SIZE),
                new EnumField(Video::ATTRIBUTE_SOURCE, VideoSource::class),
                new BooleanField(Video::ATTRIBUTE_SUBBED),
                new BooleanField(Video::ATTRIBUTE_UNCEN),
                new StringField(Video::ATTRIBUTE_TAGS, null, Category::COMPUTED()),
                new StringField(VideoResource::ATTRIBUTE_LINK, null, Category::COMPUTED()),
            ],
        );
    }
}
