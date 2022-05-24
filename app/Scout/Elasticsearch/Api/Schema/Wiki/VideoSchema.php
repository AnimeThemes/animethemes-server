<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\Base\IdField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoBasenameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoFilenameField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoLyricsField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoMimeTypeField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoNcField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoOverlapField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoPathField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoResolutionField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoSizeField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoSourceField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoSubbedField;
use App\Scout\Elasticsearch\Api\Field\Wiki\Video\VideoUncenField;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Scout\Elasticsearch\Api\Schema\Wiki\Anime\ThemeSchema;

/**
 * Class VideoSchema.
 */
class VideoSchema extends Schema
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
            new AllowedInclude(new AnimeSchema(), Video::RELATION_ANIME),
            new AllowedInclude(new EntrySchema(), Video::RELATION_ANIMETHEMEENTRIES),
            new AllowedInclude(new ThemeSchema(), Video::RELATION_ANIMETHEME),
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
            ],
        );
    }
}
