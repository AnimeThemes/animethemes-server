<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Series\SeriesCreated;
use App\Events\Series\SeriesDeleted;
use App\Events\Series\SeriesRestored;
use App\Events\Series\SeriesUpdated;
use App\Pivots\AnimeSeries;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

/**
 * Class Series
 * @package App\Models
 */
class Series extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
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
        'restored' => SeriesRestored::class,
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
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
     * Get the anime included in the series.
     *
     * @return BelongsToMany
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_series', 'series_id', 'anime_id')
            ->using(AnimeSeries::class)
            ->withTimestamps();
    }
}
