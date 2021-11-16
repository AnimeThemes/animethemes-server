<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeDeleting;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use App\Pivots\AnimeStudio;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use Database\Factories\Wiki\AnimeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Anime.
 *
 * @property int $anime_id
 * @property Collection $animesynonyms
 * @property Collection $animethemes
 * @property Collection $images
 * @property string $name
 * @property BasePivot $pivot
 * @property Collection $resources
 * @property Enum|null $season
 * @property Collection $series
 * @property string $slug
 * @property Collection $studios
 * @property string|null $synopsis
 * @property int|null $year
 *
 * @method static AnimeFactory factory(...$parameters)
 */
class Anime extends BaseModel
{
    use \ElasticScoutDriverPlus\Searchable;
    use Searchable;

    public const TABLE = 'anime';

    public const ATTRIBUTE_ID = 'anime_id';
    public const ATTRIBUTE_NAME = 'name';
    public const ATTRIBUTE_SEASON = 'season';
    public const ATTRIBUTE_SLUG = 'slug';
    public const ATTRIBUTE_SYNOPSIS = 'synopsis';
    public const ATTRIBUTE_YEAR = 'year';

    public const RELATION_ARTISTS = 'animethemes.song.artists';
    public const RELATION_ENTRIES = 'animethemes.animethemeentries';
    public const RELATION_IMAGES = 'images';
    public const RELATION_RESOURCES = 'resources';
    public const RELATION_SERIES = 'series';
    public const RELATION_SONG = 'animethemes.song';
    public const RELATION_STUDIOS = 'studios';
    public const RELATION_SYNONYMS = 'animesynonyms';
    public const RELATION_THEMES = 'animethemes';
    public const RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

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
     * @var array
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
            ->withTimestamps();
    }
}
