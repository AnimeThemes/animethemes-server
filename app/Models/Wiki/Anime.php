<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeDeleting;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\BaseModel;
use App\Models\Discord\DiscordThread;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use Database\Factories\Wiki\AnimeFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * @property int $anime_id
 * @property Collection<int, AnimeSynonym> $animesynonyms
 * @property Collection<int, AnimeTheme> $animethemes
 * @property DiscordThread|null $discordthread
 * @property Collection<int, ExternalEntry> $externalentries
 * @property Collection<int, Image> $images
 * @property AnimeMediaFormat $media_format
 * @property string $name
 * @property Collection<int, ExternalResource> $resources
 * @property AnimeSeason|null $season
 * @property Collection<int, Series> $series
 * @property string $slug
 * @property Collection<int, Studio> $studios
 * @property string|null $synopsis
 * @property int|null $year
 *
 * @method static AnimeFactory factory(...$parameters)
 */
class Anime extends BaseModel implements HasImages, HasResources, SoftDeletable
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'anime';

    final public const string ATTRIBUTE_ID = 'anime_id';
    final public const string ATTRIBUTE_MEDIA_FORMAT = 'media_format';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SEASON = 'season';
    final public const string ATTRIBUTE_SLUG = 'slug';
    final public const string ATTRIBUTE_SYNOPSIS = 'synopsis';
    final public const string ATTRIBUTE_YEAR = 'year';

    final public const string RELATION_ARTISTS = 'animethemes.song.artists';
    final public const string RELATION_AUDIO = 'animethemes.animethemeentries.videos.audio';
    final public const string RELATION_ENTRIES = 'animethemes.animethemeentries';
    final public const string RELATION_EXTERNAL_ENTRIES = 'externalentries';
    final public const string RELATION_EXTERNAL_PROFILE = 'externalentries.externalprofile';
    final public const string RELATION_GROUPS = 'animethemes.group';
    final public const string RELATION_IMAGES = 'images';
    final public const string RELATION_RESOURCES = 'resources';
    final public const string RELATION_SCRIPTS = 'animethemes.animethemeentries.videos.videoscript';
    final public const string RELATION_SERIES = 'series';
    final public const string RELATION_SONG = 'animethemes.song';
    final public const string RELATION_STUDIOS = 'studios';
    final public const string RELATION_SYNONYMS = 'animesynonyms';
    final public const string RELATION_THEMES = 'animethemes';
    final public const string RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Anime::ATTRIBUTE_NAME,
        Anime::ATTRIBUTE_SEASON,
        Anime::ATTRIBUTE_SLUG,
        Anime::ATTRIBUTE_SYNOPSIS,
        Anime::ATTRIBUTE_YEAR,
        Anime::ATTRIBUTE_MEDIA_FORMAT,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => AnimeCreated::class,
        'deleted' => AnimeDeleted::class,
        'deleting' => AnimeDeleting::class,
        'restored' => AnimeRestored::class,
        'updated' => AnimeUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Anime::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Anime::ATTRIBUTE_ID;

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(Anime::RELATION_SYNONYMS);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['synonyms'] = $this->animesynonyms->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Anime::ATTRIBUTE_SLUG;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Anime::ATTRIBUTE_SEASON => AnimeSeason::class,
            Anime::ATTRIBUTE_YEAR => 'int',
            Anime::ATTRIBUTE_MEDIA_FORMAT => AnimeMediaFormat::class,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * @return HasMany<AnimeSynonym, $this>
     */
    public function animesynonyms(): HasMany
    {
        return $this->hasMany(AnimeSynonym::class, AnimeSynonym::ATTRIBUTE_ANIME);
    }

    /**
     * @return HasOne<DiscordThread, $this>
     */
    public function discordthread(): HasOne
    {
        return $this->hasOne(DiscordThread::class, DiscordThread::ATTRIBUTE_ANIME);
    }

    /**
     * @return BelongsToMany<Series, $this, AnimeSeries>
     */
    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, AnimeSeries::TABLE, AnimeSeries::ATTRIBUTE_ANIME, AnimeSeries::ATTRIBUTE_SERIES)
            ->using(AnimeSeries::class)
            ->as(AnimeSeriesResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return HasMany<AnimeTheme, $this>
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_ANIME);
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'animeresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('animeresource')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Image, $this, Imageable, 'animeimage'>
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGEABLE_ID, Imageable::ATTRIBUTE_IMAGE)
            ->using(Imageable::class)
            ->as('animeimage')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Studio, $this, AnimeStudio>
     */
    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class, AnimeStudio::TABLE, AnimeStudio::ATTRIBUTE_ANIME, AnimeStudio::ATTRIBUTE_STUDIO)
            ->using(AnimeStudio::class)
            ->as(AnimeStudioResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return HasMany<ExternalEntry, $this>
     */
    public function externalentries(): HasMany
    {
        return $this->hasMany(ExternalEntry::class, ExternalEntry::ATTRIBUTE_ANIME);
    }
}
