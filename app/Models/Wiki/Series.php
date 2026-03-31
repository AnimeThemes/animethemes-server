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
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesJsonResource;
use App\Models\BaseModel;
use App\Pivots\Wiki\AnimeSeries;
use App\Scout\Elasticsearch\Models\Wiki\SeriesElasticModel;
use App\Scout\Typesense\Models\Wiki\SeriesTypesenseModel;
use Database\Factories\Wiki\SeriesFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;

/**
 * @property Collection<int, Anime> $anime
 * @property string $name
 * @property int $series_id
 * @property string $slug
 *
 * @method static SeriesFactory factory(...$parameters)
 */
#[Table(Series::TABLE, Series::ATTRIBUTE_ID)]
class Series extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'series';

    final public const string ATTRIBUTE_ID = 'series_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_ANIME_SYNONYMS = 'anime.synonyms';

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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Series::ATTRIBUTE_NAME,
        Series::ATTRIBUTE_SLUG,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Series::ATTRIBUTE_NAME => 'string',
            Series::ATTRIBUTE_SLUG => 'string',
        ];
    }

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
        return match (Config::get('scout.driver')) {
            'elastic' => SeriesElasticModel::toSearchableArray($this),
            'typesense' => SeriesTypesenseModel::toSearchableArray($this),
            default => throw new RuntimeException('Unsupported search driver configured.'),
        };
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
            ->as(AnimeSeriesJsonResource::$wrap)
            ->withTimestamps();
    }
}
