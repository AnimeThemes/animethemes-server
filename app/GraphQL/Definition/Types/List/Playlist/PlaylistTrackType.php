<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List\Playlist;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackEntryIdField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackIdField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackNextField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPlaylistField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPreviousField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackVideoIdField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\List\Playlist\PlaylistTrack;

class PlaylistTrackType extends EloquentType
{
    public function description(): string
    {
        return "Represents an entry in a playlist.\n\nFor example, a \"/r/anime's Best OPs and EDs of 2022\" playlist may contain a track for the ParipiKoumei-OP1.webm video.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new PlaylistType(), PlaylistTrack::RELATION_PLAYLIST)
                ->notNullable(),
            new BelongsToRelation(new AnimeThemeEntryType(), PlaylistTrack::RELATION_ENTRY)
                ->notNullable(),
            new BelongsToRelation(new VideoType(), PlaylistTrack::RELATION_VIDEO)
                ->notNullable(),
            new BelongsToRelation(new PlaylistTrackType(), PlaylistTrack::RELATION_NEXT),
            new BelongsToRelation(new PlaylistTrackType(), PlaylistTrack::RELATION_PREVIOUS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new PlaylistTrackIdField(),
            new PlaylistTrackEntryIdField(),
            new PlaylistTrackVideoIdField(),
            new PlaylistTrackNextField(),
            new PlaylistTrackPreviousField(),
            new PlaylistTrackPlaylistField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
