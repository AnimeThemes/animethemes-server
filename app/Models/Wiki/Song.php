<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongDeleting;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\SongResource;
use Database\Factories\Wiki\SongFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Song.
 *
 * @property Collection<int, AnimeTheme> $animethemes
 * @property Collection<int, Artist> $artists
 * @property Collection<int, ExternalResource> $resources
 * @property int $song_id
 * @property string|null $title
 *
 * @method static SongFactory factory(...$parameters)
 */
class Song extends BaseModel
{
    use Actionable;
    use Searchable;

    final public const TABLE = 'songs';

    final public const ATTRIBUTE_ID = 'song_id';
    final public const ATTRIBUTE_TITLE = 'title';

    final public const RELATION_ANIME = 'animethemes.anime';
    final public const RELATION_ANIMETHEMES = 'animethemes';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_RESOURCES = 'resources';
    final public const RELATION_THEME_GROUPS = 'animethemes.group';
    final public const RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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

    public function getSubName(): string
    {
        return $this->animethemes->first()->anime->getName();
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
            ->as(ArtistSongResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the resources for the song.
     *
     * @return BelongsToMany
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(ExternalResource::class, SongResource::TABLE, Song::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_ID)
            ->using(SongResource::class)
            ->withPivot(SongResource::ATTRIBUTE_AS)
            ->as(SongResourceResource::$wrap)
            ->withTimestamps();
    }
}
