<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\AnimeSeriesFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeSeries.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property Series $series
 * @property int $series_id
 *
 * @method static AnimeSeriesFactory factory(...$parameters)
 */
class AnimeSeries extends BasePivot
{
    final public const TABLE = 'anime_series';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_SERIES = 'series_id';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_SERIES = 'series';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeSeries::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            AnimeSeries::ATTRIBUTE_ANIME,
            AnimeSeries::ATTRIBUTE_SERIES,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        AnimeSeries::ATTRIBUTE_ANIME,
        AnimeSeries::ATTRIBUTE_SERIES,
    ];

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
     * @return BelongsTo<Anime, AnimeSeries>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeSeries::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the series that owns the anime series.
     *
     * @return BelongsTo<Series, AnimeSeries>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class, AnimeSeries::ATTRIBUTE_SERIES);
    }
}
