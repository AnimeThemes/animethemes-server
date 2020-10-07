<?php

namespace App\Models;

use App\Enums\Season;
use App\ScoutElastic\AnimeIndexConfigurator;
use App\ScoutElastic\AnimeSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Anime extends Model implements Auditable
{

    use CastsEnums, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['alias', 'name', 'year', 'season', 'synopsis', 'cover'];

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
        AnimeSearchRule::class
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text'
            ],
            'synonyms' => [
                'type' => 'nested',
                'properties' => [
                    'text' => [
                        'type' => 'text'
                    ]
                ]
            ]
        ]
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'alias';
    }

    /**
     * @var array
     */
    protected $enumCasts = [
        'season' => Season::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'season' => 'int',
    ];

    /**
     * Get the synonyms for the anime
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function synonyms() {
        return $this->hasMany('App\Models\Synonym', 'anime_id', 'anime_id');
    }

    /**
     * Get the series the anime is included in
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function series() {
        return $this->belongsToMany('App\Models\Series', 'anime_series', 'anime_id', 'series_id');
    }

    /**
     * Get the themes for the anime
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme', 'anime_id', 'anime_id');
    }

    /**
     * Get the resources for the anime
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function externalResources() {
        return $this->belongsToMany('App\Models\ExternalResource', 'anime_resource', 'anime_id', 'resource_id')->withPivot('as');
    }
}
