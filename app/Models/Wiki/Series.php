<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Series\SeriesCreated;
use App\Events\Wiki\Series\SeriesDeleted;
use App\Events\Wiki\Series\SeriesRestored;
use App\Events\Wiki\Series\SeriesUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Models\BaseModel;
use App\Pivots\Wiki\AnimeSeries;
use Database\Factories\Wiki\SeriesFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Anime> $anime
 * @property string $name
 * @property int $series_id
 * @property string $slug
 *
 * @method static SeriesFactory factory(...$parameters)
 */
class Series extends BaseModel implements SoftDeletable
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'series';

    final public const string ATTRIBUTE_ID = 'series_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_ANIME_SYNONYMS = 'anime.animesynonyms';

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
     * @var array<string, class-string>
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
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(Series::RELATION_ANIME_SYNONYMS);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['anime'] = $this->anime->map(
            fn (Anime $anime): array => $anime->toSearchableArray()
        )->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Series::ATTRIBUTE_SLUG;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * @return BelongsToMany<Anime, $this, AnimeSeries>
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeSeries::TABLE, AnimeSeries::ATTRIBUTE_SERIES, AnimeSeries::ATTRIBUTE_ANIME)
            ->using(AnimeSeries::class)
            ->as(AnimeSeriesResource::$wrap)
            ->withTimestamps();
    }
}
