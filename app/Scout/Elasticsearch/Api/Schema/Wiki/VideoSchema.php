<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\Wiki;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Resources\Wiki\Resource\VideoJsonResource;
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

class VideoSchema extends Schema
{
    public function type(): string
    {
        return VideoJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Video::RELATION_ANIME),
            new AllowedInclude(new AudioSchema(), Video::RELATION_AUDIO),
            new AllowedInclude(new EntrySchema(), Video::RELATION_ANIMETHEMEENTRIES),
            new AllowedInclude(new GroupSchema(), Video::RELATION_GROUP),
            new AllowedInclude(new ThemeSchema(), Video::RELATION_ANIMETHEME),
        ]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Video::ATTRIBUTE_ID),
                new VideoBasenameField($this),
                new VideoFilenameField($this),
                new VideoLyricsField($this),
                new VideoMimeTypeField($this),
                new VideoNcField($this),
                new VideoOverlapField($this),
                new VideoPathField($this),
                new VideoResolutionField($this),
                new VideoSizeField($this),
                new VideoSourceField($this),
                new VideoSubbedField($this),
                new VideoUncenField($this),
            ],
        );
    }
}
