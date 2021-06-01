<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\ArtistSong\ArtistSongUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistSong
 * @package App\Pivots
 */
class ArtistSong extends BasePivot
{
    use HasFactory;

    /**
     * @var array
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
        return $this->belongsTo('App\Models\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the song that owns the artist song.
     *
     * @return BelongsTo
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo('App\Models\Song', 'song_id', 'song_id');
    }
}
