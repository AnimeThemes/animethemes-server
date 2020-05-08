<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Series extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['alias', 'name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'series';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'series_id';

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
     * Get the anime included in the series
     */
    public function anime() {
        return $this->belongsToMany('App\Models\Anime', 'anime_series', 'series_id', 'anime_id');
    }
}
