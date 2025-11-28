<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
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
 * @property Collection<int, AnimeTheme> $animethemes
 * @property Collection<int, Artist> $artists
 * @property Collection<int, Performance> $performances
 * @property Collection<int, ExternalResource> $resources
 * @property int $song_id
 * @property string|null $title
 * @property string|null $title_native
 *
 * @method static SongFactory factory(...$parameters)
 */
class Song extends BaseModel implements HasResources, SoftDeletable
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'songs';

    final public const string ATTRIBUTE_ID = 'song_id';
    final public const string ATTRIBUTE_TITLE = 'title';
    final public const string ATTRIBUTE_TITLE_NATIVE = 'title_native';

    final public const string RELATION_ANIME = 'animethemes.anime';
    final public const string RELATION_ANIMETHEMES = 'animethemes';
    final public const string RELATION_ARTISTS = 'artists';
    final public const string RELATION_PERFORMANCES = 'performances';
    final public const string RELATION_PERFORMANCE_ARTISTS = 'performances.artist';
    final public const string RELATION_RESOURCES = 'resources';
    final public const string RELATION_THEME_GROUPS = 'animethemes.group';
    final public const string RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Song::ATTRIBUTE_TITLE,
        Song::ATTRIBUTE_TITLE_NATIVE,
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

    public function getName(): string
    {
        if (blank($this->title)) {
            return strval($this->getKey());
        }

        return $this->title;
    }

    public function getSubtitle(): string
    {
        if ($this->animethemes()->count() !== 0 && $this->animethemes->first()->anime !== null) {
            return "{$this->animethemes->first()->anime->getName()} {$this->animethemes->first()->slug}";
        }

        return $this->title_native ?? strval($this->getKey());
    }

    /**
     * @return HasMany<AnimeTheme, $this>
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_SONG);
    }

    /**
     * @return BelongsToMany<Artist, $this, ArtistSong>
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistSong::TABLE, ArtistSong::ATTRIBUTE_SONG, ArtistSong::ATTRIBUTE_ARTIST)
            ->using(ArtistSong::class)
            ->withPivot([ArtistSong::ATTRIBUTE_ALIAS, ArtistSong::ATTRIBUTE_AS])
            ->as(ArtistSongResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return HasMany<Performance, $this>
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class, Performance::ATTRIBUTE_SONG);
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'songresource'>
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
