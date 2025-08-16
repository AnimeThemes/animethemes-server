<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongDeleting;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistSong;
use Database\Factories\Wiki\SongFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * Class Song.
 *
 * @property Collection<int, AnimeTheme> $animethemes
 * @property Collection<int, Artist> $artists
 * @property Collection<int, Performance> $performances
 * @property Collection<int, ExternalResource> $resources
 * @property int $song_id
 * @property string|null $title
 *
 * @method static SongFactory factory(...$parameters)
 */
class Song extends BaseModel implements HasResources, SoftDeletable
{
    use HasFactory;
    use Reportable;
    use Searchable;
    use SoftDeletes;

    final public const TABLE = 'songs';

    final public const ATTRIBUTE_ID = 'song_id';
    final public const ATTRIBUTE_TITLE = 'title';

    final public const RELATION_ANIME = 'animethemes.anime';
    final public const RELATION_ANIMETHEMES = 'animethemes';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_PERFORMANCES = 'performances';
    final public const RELATION_PERFORMANCE_ARTISTS = 'performances.artist';
    final public const RELATION_RESOURCES = 'resources';
    final public const RELATION_THEME_GROUPS = 'animethemes.group';
    final public const RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Song::ATTRIBUTE_TITLE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
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
     */
    public function getName(): string
    {
        if (empty($this->title)) {
            return strval($this->getKey());
        }

        return $this->title;
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return $this->animethemes()->count() !== 0 && $this->animethemes->first()->anime !== null
            ? "{$this->animethemes->first()->anime->getName()} {$this->animethemes->first()->slug}"
            : $this->getName();
    }

    /**
     * Get the eager loads needed to the subtitle.
     *
     * @return string[]
     */
    public static function getEagerLoadsForSubtitle(): array
    {
        return [
            Song::RELATION_ANIME,
        ];
    }

    /**
     * Get the anime themes that use this song.
     *
     * @return HasMany<AnimeTheme, $this>
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
            ->withPivot([ArtistSong::ATTRIBUTE_ALIAS, ArtistSong::ATTRIBUTE_AS])
            ->as(ArtistSongResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the performances of the song.
     *
     * @return HasMany<Performance, $this>
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class, Performance::ATTRIBUTE_SONG);
    }

    /**
     * Get the resources for the song through the resourceable morph pivot.
     *
     * @return MorphToMany
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('songresource')
            ->withTimestamps();
    }
}
