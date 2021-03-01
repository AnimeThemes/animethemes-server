<?php

namespace App\Models;

use App\Contracts\Nameable;
use App\Enums\AnimeSeason;
use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeDeleting;
use App\Events\Anime\AnimeUpdated;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use BenSampo\Enum\Traits\CastsEnums;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Anime extends Model implements Auditable, Nameable
{
    use CastsEnums, HasFactory, QueryDsl, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
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
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * @var array
     */
    protected $enumCasts = [
        'season' => AnimeSeason::class,
    ];

    /**
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the synonyms for the anime.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function synonyms()
    {
        return $this->hasMany('App\Models\Synonym', 'anime_id', 'anime_id');
    }

    /**
     * Get the series the anime is included in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function series()
    {
        return $this->belongsToMany('App\Models\Series', 'anime_series', 'anime_id', 'series_id')->using(AnimeSeries::class);
    }

    /**
     * Get the themes for the anime.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function themes()
    {
        return $this->hasMany('App\Models\Theme', 'anime_id', 'anime_id');
    }

    /**
     * Get the resources for the anime.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function externalResources()
    {
        return $this->belongsToMany('App\Models\ExternalResource', 'anime_resource', 'anime_id', 'resource_id')->using(AnimeResource::class)->withPivot('as');
    }

    /**
     * Get the images for the anime.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'anime_image', 'anime_id', 'image_id')->using(AnimeImage::class);
    }
}
