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
 * @property string $as
 * @property Artist $artist
 * @property Song $song
 * @method static ArtistSongFactory factory(...$parameters)
 */
class ArtistSong extends BasePivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['as'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist_song';

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
        return $this->belongsTo('App\Models\Wiki\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the song that owns the artist song.
     *
     * @return BelongsTo
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Song', 'song_id', 'song_id');
    }
}
