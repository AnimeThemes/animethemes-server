<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeDeleting;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use BenSampo\Enum\Enum;
use Database\Factories\Wiki\AnimeFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Anime.
 *
 * @property int $anime_id
 * @property Collection<int, AnimeSynonym> $animesynonyms
 * @property Collection<int, AnimeTheme> $animethemes
 * @property Collection<int, Image> $images
 * @property string $name
 * @property Collection<int, ExternalResource> $resources
 * @property Enum|null $season
 * @property Collection<int, Series> $series
 * @property string $slug
 * @property Collection<int, Studio> $studios
 * @property string|null $synopsis
 * @property int|null $year
 *
 * @method static AnimeFactory factory(...$parameters)
 */
class Anime extends BaseModel
{
    use Actionable;
    use Searchable;

    final public const TABLE = 'anime';

    final public const ATTRIBUTE_ID = 'anime_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SEASON = 'season';
    final public const ATTRIBUTE_SLUG = 'slug';
    final public const ATTRIBUTE_SYNOPSIS = 'synopsis';
    final public const ATTRIBUTE_YEAR = 'year';

    final public const RELATION_ARTISTS = 'animethemes.song.artists';
    final public const RELATION_AUDIO = 'animethemes.animethemeentries.videos.audio';
    final public const RELATION_ENTRIES = 'animethemes.animethemeentries';
    final public const RELATION_IMAGES = 'images';
    final public const RELATION_RESOURCES = 'resources';
    final public const RELATION_SCRIPTS = 'animethemes.animethemeentries.videos.videoscript';
    final public const RELATION_SERIES = 'series';
    final public const RELATION_SONG = 'animethemes.song';
    final public const RELATION_STUDIOS = 'studios';
    final public const RELATION_SYNONYMS = 'animesynonyms';
    final public const RELATION_THEMES = 'animethemes';
    final public const RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Anime::ATTRIBUTE_NAME,
        Anime::ATTRIBUTE_SEASON,
        Anime::ATTRIBUTE_SLUG,
        Anime::ATTRIBUTE_SYNOPSIS,
        Anime::ATTRIBUTE_YEAR,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
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
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(Anime::RELATION_SYNONYMS);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
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
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Anime::ATTRIBUTE_SLUG;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        Anime::ATTRIBUTE_SEASON => AnimeSeason::class,
        Anime::ATTRIBUTE_YEAR => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the synonyms for the anime.
     *
     * @return HasMany
     */
    public function animesynonyms(): HasMany
    {
        return $this->hasMany(AnimeSynonym::class, AnimeSynonym::ATTRIBUTE_ANIME);
    }

    /**
     * Get the series the anime is included in.
     *
     * @return BelongsToMany
     */
    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, AnimeSeries::TABLE, Anime::ATTRIBUTE_ID, Series::ATTRIBUTE_ID)
            ->using(AnimeSeries::class)
            ->as(AnimeSeriesResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the themes for the anime.
     *
     * @return HasMany
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_ANIME);
    }

    /**
     * Get the resources for the anime.
     *
     * @return BelongsToMany
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(ExternalResource::class, AnimeResource::TABLE, Anime::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_ID)
            ->using(AnimeResource::class)
            ->withPivot(AnimeResource::ATTRIBUTE_AS)
            ->as(AnimeResourceResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the images for the anime.
     *
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, AnimeImage::TABLE, Anime::ATTRIBUTE_ID, Image::ATTRIBUTE_ID)
            ->using(AnimeImage::class)
            ->as(AnimeImageResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the studios that produced the anime.
     *
     * @return BelongsToMany
     */
    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class, AnimeStudio::TABLE, Anime::ATTRIBUTE_ID, Studio::ATTRIBUTE_ID)
            ->using(AnimeStudio::class)
            ->as(AnimeStudioResource::$wrap)
            ->withTimestamps();
    }
}
