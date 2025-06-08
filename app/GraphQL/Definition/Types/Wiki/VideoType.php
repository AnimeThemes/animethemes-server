<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoBasenameField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoFilenameField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoLikesCountField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoLinkField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoLyricsField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoMimetypeField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoNcField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoOverlapField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoPathField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoResolutionField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoSizeField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoSourceField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoSubbedField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoTagsField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoUncenField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoViewsCountField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\HasOneRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\Video\VideoScriptType;
use App\Models\Wiki\Video;

/**
 * Class VideoType.
 */
class VideoType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a WebM of an anime theme.\n\nFor example, the video Bakemonogatari-OP1.webm represents the WebM of the Bakemonogatari OP1 theme.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new AnimeThemeEntryType(), Video::RELATION_ANIMETHEMEENTRIES),
            new HasOneRelation(new VideoScriptType(), Video::RELATION_SCRIPT),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(Video::ATTRIBUTE_ID),
            new VideoBasenameField(),
            new VideoFilenameField(),
            new VideoLyricsField(),
            new VideoMimetypeField(),
            new VideoNcField(),
            new VideoOverlapField(),
            new LocalizedEnumField(new VideoOverlapField()),
            new VideoPathField(),
            new VideoResolutionField(),
            new VideoSizeField(),
            new VideoSourceField(),
            new LocalizedEnumField(new VideoSourceField()),
            new VideoSubbedField(),
            new VideoUncenField(),
            new VideoTagsField(),
            new VideoLinkField(),
            new VideoLikesCountField(),
            new VideoViewsCountField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
