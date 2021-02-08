<?php

namespace App\Pivots;

use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AnimeSeries extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_series';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnimeSeriesCreated::class,
        'deleted' => AnimeSeriesDeleted::class,
    ];

    /**
     * Gets the anime that owns the anime series.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anime()
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the series that owns the anime series.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function series()
    {
        return $this->belongsTo('App\Models\Series', 'series_id', 'series_id');
    }
}
