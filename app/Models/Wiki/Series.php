<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Models\BaseModel;
use App\Pivots\Wiki\AnimeSeries;
use Database\Factories\Wiki\SeriesFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

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
    use Searchable;

    final public const TABLE = 'series';

    final public const ATTRIBUTE_ID = 'series_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';

    final public const RELATION_ANIME = 'anime';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * Get the anime included in the series.
     *
     * @return BelongsToMany<Anime, $this>
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeSeries::TABLE, Series::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeSeries::class)
            ->as(AnimeSeriesResource::$wrap)
            ->withTimestamps();
    }
}
