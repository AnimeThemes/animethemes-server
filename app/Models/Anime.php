<?php

namespace App\Models;

use App\Enums\AnimeSeason;
use App\Http\Controllers\Api\AnimeController;
use App\ScoutElastic\AnimeIndexConfigurator;
use App\ScoutElastic\AnimeSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Anime extends Model implements Auditable
{
    use CastsEnums, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['slug', 'name', 'year', 'season', 'synopsis'];

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
     * @var string
     */
    protected $indexConfigurator = AnimeIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        AnimeSearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
            ],
            'synonyms' => [
                'type' => 'nested',
                'properties' => [
                    'text' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ],
    ];

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
    ];

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static $allowedIncludePaths = [
        'synonyms',
        'series',
        'themes',
        'themes.entries',
        'themes.entries.videos',
        'themes.song',
        'themes.song.artists',
        'externalResources',
        'images',
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'anime_id',
        'created_at',
        'updated_at',
        'slug',
        'name',
        'year',
        'season',
    ];

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
        return $this->belongsToMany('App\Models\Series', 'anime_series', 'anime_id', 'series_id');
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
        return $this->belongsToMany('App\Models\ExternalResource', 'anime_resource', 'anime_id', 'resource_id')->withPivot('as');
    }

    /**
     * Get the images for the anime.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'anime_image', 'anime_id', 'image_id');
    }

    public static function applyFilters($anime, $parser)
    {
        // apply filters
        if ($parser->hasFilter(AnimeController::YEAR_QUERY)) {
            $anime = $anime->whereIn(AnimeController::YEAR_QUERY, $parser->getFilter(AnimeController::YEAR_QUERY));
        }
        if ($parser->hasFilter(AnimeController::SEASON_QUERY)) {
            $anime = $anime->whereIn(AnimeController::SEASON_QUERY, $parser->getEnumFilter(AnimeController::SEASON_QUERY, AnimeSeason::class));
        }

        return $anime;
    }
}
