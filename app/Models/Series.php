<?php

namespace App\Models;

use App\Events\Series\SeriesCreated;
use App\Events\Series\SeriesDeleted;
use App\Events\Series\SeriesUpdated;
use App\Pivots\AnimeSeries;
use App\Contracts\Nameable;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Series extends Model implements Auditable, Nameable
{
    use CustomSearch, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['slug', 'name'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SeriesCreated::class,
        'deleted' => SeriesDeleted::class,
        'updated' => SeriesUpdated::class,
    ];

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
        return 'slug';
    }

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
     * Get the anime included in the series.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_series', 'series_id', 'anime_id')->using(AnimeSeries::class);
    }
}
