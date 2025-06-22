<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List\Playlist;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackIdField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class PlaylistTrackType.
 */
class PlaylistTrackType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an entry in a playlist.\n\nFor example, a \"/r/anime's Best OPs and EDs of 2022\" playlist may contain a track for the ParipiKoumei-OP1.webm video.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new PlaylistType(), PlaylistTrack::RELATION_PLAYLIST, nullable: false),
            new BelongsToRelation(new AnimeThemeEntryType(), PlaylistTrack::RELATION_ENTRY, nullable: false),
            new BelongsToRelation(new VideoType(), PlaylistTrack::RELATION_VIDEO, nullable: false),
            new BelongsToRelation(new PlaylistTrackType(), PlaylistTrack::RELATION_NEXT),
            new BelongsToRelation(new PlaylistTrackType(), PlaylistTrack::RELATION_PREVIOUS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new PlaylistTrackIdField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
