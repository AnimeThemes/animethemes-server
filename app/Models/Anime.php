<?php

namespace App\Models;

use App\Enums\Season;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Anime extends Model implements Auditable
{

    use CastsEnums;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['alias', 'name', 'year', 'season'];

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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'alias';
    }

    protected $enumCasts = [
        'season' => Season::class,
    ];

    protected $casts = [
        'season' => 'int',
    ];

    /**
     * Get the synonyms for the anime
     */
    public function synonyms() {
        return $this->hasMany('App\Models\Synonym');
    }

    /**
     * Get the series the anime is included in
     */
    public function series() {
        return $this->belongsToMany('App\Models\Series');
    }

    /**
     * Get the themes for the anime
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme');
    }

    /**
     * Get the resources for the anime
     */
    public function resources() {
        return $this->belongsToMany('App\Models\Resource');
    }
}
