<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\ArtistSong\ArtistSongUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Database\Factories\Pivots\ArtistSongFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistSong.
 *
 * @property Artist $artist
 * @property string $as
 * @property Song $song
 *
 * @method static ArtistSongFactory factory(...$parameters)
 */
class ArtistSong extends BasePivot
{
    final public const TABLE = 'artist_song';

    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_SONG = 'song_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ArtistSong::ATTRIBUTE_AS,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ArtistSong::TABLE;

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
     * @return BelongsTo
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistSong::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the song that owns the artist song.
     *
     * @return BelongsTo
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, ArtistSong::ATTRIBUTE_SONG);
    }
}
