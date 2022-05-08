<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Video\VideoBasenameField;
use App\Http\Api\Field\Wiki\Video\VideoFilenameField;
use App\Http\Api\Field\Wiki\Video\VideoLinkField;
use App\Http\Api\Field\Wiki\Video\VideoLyricsField;
use App\Http\Api\Field\Wiki\Video\VideoMimeTypeField;
use App\Http\Api\Field\Wiki\Video\VideoNcField;
use App\Http\Api\Field\Wiki\Video\VideoOverlapField;
use App\Http\Api\Field\Wiki\Video\VideoPathField;
use App\Http\Api\Field\Wiki\Video\VideoResolutionField;
use App\Http\Api\Field\Wiki\Video\VideoSizeField;
use App\Http\Api\Field\Wiki\Video\VideoSourceField;
use App\Http\Api\Field\Wiki\Video\VideoSubbedField;
use App\Http\Api\Field\Wiki\Video\VideoTagsField;
use App\Http\Api\Field\Wiki\Video\VideoUncenField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;

/**
 * Class VideoSchema.
 */
class VideoSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Video::class;
    }

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
            new AllowedInclude(AnimeSchema::class, Video::RELATION_ANIME),
            new AllowedInclude(EntrySchema::class, Video::RELATION_ANIMETHEMEENTRIES),
            new AllowedInclude(ThemeSchema::class, Video::RELATION_ANIMETHEME),
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
                new IdField(Video::ATTRIBUTE_ID),
                new VideoBasenameField(),
                new VideoFilenameField(),
                new VideoLyricsField(),
                new VideoMimeTypeField(),
                new VideoNcField(),
                new VideoOverlapField(),
                new VideoPathField(),
                new VideoResolutionField(),
                new VideoSizeField(),
                new VideoSourceField(),
                new VideoSubbedField(),
                new VideoUncenField(),
                new VideoTagsField(),
                new VideoLinkField(),
            ],
        );
    }
}
