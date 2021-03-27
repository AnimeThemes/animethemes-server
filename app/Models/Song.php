<?php

namespace App\Models;

use App\Events\Song\SongCreated;
use App\Events\Song\SongDeleted;
use App\Events\Song\SongDeleting;
use App\Events\Song\SongRestored;
use App\Events\Song\SongUpdated;
use App\Pivots\ArtistSong;
use ElasticScoutDriverPlus\QueryDsl;
use Laravel\Scout\Searchable;

class Song extends BaseModel
{
    use QueryDsl, Searchable;

    /**
     * @var array
     */
    protected $fillable = ['title'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SongCreated::class,
        'deleted' => SongDeleted::class,
        'deleting' => SongDeleting::class,
        'restored' => SongRestored::class,
        'updated' => SongUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'song';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'song_id';

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        if (empty($this->title)) {
            return $this->song_id;
        }

        return $this->title;
    }

    /**
     * Get the themes that use this song.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function themes()
    {
        return $this->hasMany('App\Models\Theme', 'song_id', 'song_id');
    }

    /**
     * Get the artists included in the performance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_song', 'song_id', 'artist_id')
            ->using(ArtistSong::class)
            ->withPivot('as')
            ->withTimestamps();
    }
}
