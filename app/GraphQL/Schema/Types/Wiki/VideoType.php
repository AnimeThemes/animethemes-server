<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Fields\Relations\HasOneRelation;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoBasenameField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoFilenameField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoLinkField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoLyricsField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoMimetypeField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoNcField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoOverlapField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoPathField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoPriorityField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoResolutionField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoSizeField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoSourceField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoSubbedField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoTagsField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoUncenField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoViewsCountField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeThemeEntryVideoType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Types\Wiki\Video\VideoScriptType;
use App\Models\Wiki\Video;

class VideoType extends EloquentType
{
    public function description(): string
    {
        return "Represents a WebM of an anime theme.\n\nFor example, the video Bakemonogatari-OP1.webm represents the WebM of the Bakemonogatari OP1 theme.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Video::ATTRIBUTE_ID, Video::class),
            new VideoBasenameField(),
            new VideoFilenameField(),
            new VideoLyricsField(),
            new VideoMimetypeField(),
            new VideoNcField(),
            new VideoOverlapField(),
            new LocalizedEnumField(new VideoOverlapField()),
            new VideoPathField(),
            new VideoPriorityField(),
            new VideoResolutionField(),
            new VideoSizeField(),
            new VideoSourceField(),
            new LocalizedEnumField(new VideoSourceField()),
            new VideoSubbedField(),
            new VideoUncenField(),
            new VideoTagsField(),
            new VideoLinkField(),
            new VideoViewsCountField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new BelongsToRelation(new AudioType(), Video::RELATION_AUDIO),
            new BelongsToManyRelation($this, new AnimeThemeEntryType(), Video::RELATION_ANIMETHEMEENTRIES, new AnimeThemeEntryVideoType()),
            new HasManyRelation(new PlaylistTrackType(), Video::RELATION_TRACKS),
            new HasOneRelation(new VideoScriptType(), Video::RELATION_SCRIPT),
        ];
    }
}
