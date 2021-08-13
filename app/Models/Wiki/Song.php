<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongDeleting;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Models\BaseModel;
use App\Pivots\ArtistSong;
use App\Pivots\BasePivot;
use Database\Factories\Wiki\SongFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Song.
 *
 * @property int $song_id
 * @property string|null $title
 * @property Collection $themes
 * @property Collection $artists
 * @property BasePivot $pivot
 * @method static SongFactory factory(...$parameters)
 */
class Song extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
    public function getName(): string
    {
        if (empty($this->title)) {
            return strval($this->getKey());
        }

        return $this->title;
    }

    /**
     * Get the themes that use this song.
     *
     * @return HasMany
     */
    public function themes(): HasMany
    {
        return $this->hasMany('App\Models\Wiki\Anime\Theme', 'song_id', 'song_id');
    }

    /**
     * Get the artists included in the performance.
     *
     * @return BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Artist', 'artist_song', 'song_id', 'artist_id')
            ->using(ArtistSong::class)
            ->withPivot('as')
            ->withTimestamps();
    }
}
