<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Database\Factories\Pivots\AnimeSeriesFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeSeries.
 *
 * @property Anime $anime
 * @property Series $series
 * @method static AnimeSeriesFactory factory(...$parameters)
 */
class AnimeSeries extends BasePivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_series';

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
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the series that owns the anime series.
     *
     * @return BelongsTo
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Series', 'series_id', 'series_id');
    }
}
