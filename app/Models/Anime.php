<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AnimeSeason;
use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeDeleting;
use App\Events\Anime\AnimeRestored;
use App\Events\Anime\AnimeUpdated;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use BenSampo\Enum\Traits\CastsEnums;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

/**
 * Class Anime.
 */
class Anime extends BaseModel
{
    use CastsEnums;
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['slug', 'name', 'year', 'season', 'synopsis'];

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
    protected $table = 'anime';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'anime_id';

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('synonyms');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['synonyms'] = $this->synonyms->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        'season' => AnimeSeason::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'season' => 'int',
        'year' => 'int',
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
    public function synonyms(): HasMany
    {
        return $this->hasMany('App\Models\Synonym', 'anime_id', 'anime_id');
    }

    /**
     * Get the series the anime is included in.
     *
     * @return BelongsToMany
     */
    public function series(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Series', 'anime_series', 'anime_id', 'series_id')
            ->using(AnimeSeries::class)
            ->withTimestamps();
    }

    /**
     * Get the themes for the anime.
     *
     * @return HasMany
     */
    public function themes(): HasMany
    {
        return $this->hasMany('App\Models\Theme', 'anime_id', 'anime_id');
    }

    /**
     * Get the resources for the anime.
     *
     * @return BelongsToMany
     */
    public function externalResources(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ExternalResource', 'anime_resource', 'anime_id', 'resource_id')
            ->using(AnimeResource::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the images for the anime.
     *
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Image', 'anime_image', 'anime_id', 'image_id')
            ->using(AnimeImage::class)
            ->withTimestamps();
    }
}
