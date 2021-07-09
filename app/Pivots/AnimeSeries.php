<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeSeries.
 */
class AnimeSeries extends BasePivot
{
    use HasFactory;

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
     * @var array<string, string>
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