<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Models\BaseModel;
use App\Pivots\AnimeSeries;
use Database\Factories\Wiki\SeriesFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Series.
 *
 * @property Collection<int, Anime> $anime
 * @property string $name
 * @property int $series_id
 * @property string $slug
 *
 * @method static SeriesFactory factory(...$parameters)
 */
class Series extends BaseModel
{
    use Actionable;
    use Searchable;

    final public const TABLE = 'series';

    final public const ATTRIBUTE_ID = 'series_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';

    final public const RELATION_ANIME = 'anime';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Series::ATTRIBUTE_NAME,
        Series::ATTRIBUTE_SLUG,
    ];

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
    protected $table = Series::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Series::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Series::ATTRIBUTE_SLUG;
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
        return $this->belongsToMany(Anime::class, AnimeSeries::TABLE, Series::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeSeries::class)
            ->withTimestamps();
    }
}
