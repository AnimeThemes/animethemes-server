<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\ArtistSong\ArtistSongAliasField;
use App\Http\Api\Field\Pivot\Wiki\ArtistSong\ArtistSongArtistIdField;
use App\Http\Api\Field\Pivot\Wiki\ArtistSong\ArtistSongAsField;
use App\Http\Api\Field\Pivot\Wiki\ArtistSong\ArtistSongSongIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Pivots\Wiki\ArtistSong;

/**
 * Class ArtistSongSchema.
 */
class ArtistSongSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistSongResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new ArtistSchema(), ArtistSong::RELATION_ARTIST),
                new AllowedInclude(new SongSchema(), ArtistSong::RELATION_SONG),
            ]),
            []
        );
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new ArtistSongArtistIdField($this),
            new ArtistSongSongIdField($this),
            new ArtistSongAliasField($this),
            new ArtistSongAsField($this),
        ];
    }
}
