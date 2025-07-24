<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\Wiki\ArtistSong\ArtistSongUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\ArtistSongFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistSong.
 *
 * @property Artist $artist
 * @property int $artist_id
 * @property string|null $alias
 * @property string|null $as
 * @property Song $song
 * @property int $song_id
 *
 * @method static ArtistSongFactory factory(...$parameters)
 */
class ArtistSong extends BasePivot
{
    final public const TABLE = 'artist_song';

    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_ALIAS = 'alias';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_SONG = 'song_id';

    final public const RELATION_ARTIST = 'artist';
    final public const RELATION_SONG = 'song';

    /**
     * The table associated with the model.
     */
    protected $table = ArtistSong::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ArtistSong::ATTRIBUTE_ARTIST,
        ArtistSong::ATTRIBUTE_ALIAS,
        ArtistSong::ATTRIBUTE_AS,
        ArtistSong::ATTRIBUTE_SONG,
    ];

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            ArtistSong::ATTRIBUTE_ARTIST,
            ArtistSong::ATTRIBUTE_SONG,
        ];
    }

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ArtistSongCreated::class,
        'deleted' => ArtistSongDeleted::class,
        'updated' => ArtistSongUpdated::class,
    ];

    /**
     * Gets the artist that owns the artist song.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistSong::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the song that owns the artist song.
     *
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, ArtistSong::ATTRIBUTE_SONG);
    }
}
