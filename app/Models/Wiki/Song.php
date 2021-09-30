<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongDeleting;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
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
 * @property Collection $animethemes
 * @property Collection $artists
 * @property BasePivot $pivot
 * @property int $song_id
 * @property string|null $title
 * @method static SongFactory factory(...$parameters)
 */
class Song extends BaseModel
{
    use QueryDsl;
    use Searchable;

    public const TABLE = 'songs';

    public const ATTRIBUTE_ID = 'song_id';
    public const ATTRIBUTE_TITLE = 'title';

    public const RELATION_ANIME = 'animethemes.anime';
    public const RELATION_ANIMETHEMES = 'animethemes';
    public const RELATION_ARTISTS = 'artists';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Song::ATTRIBUTE_TITLE,
    ];

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
    protected $table = Song::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Song::ATTRIBUTE_ID;

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
     * Get the anime themes that use this song.
     *
     * @return HasMany
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_SONG);
    }

    /**
     * Get the artists included in the performance.
     *
     * @return BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistSong::TABLE, Song::ATTRIBUTE_ID, Artist::ATTRIBUTE_ID)
            ->using(ArtistSong::class)
            ->withPivot(ArtistSong::ATTRIBUTE_AS)
            ->withTimestamps();
    }
}
