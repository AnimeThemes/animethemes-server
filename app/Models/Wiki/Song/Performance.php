<?php

declare(strict_types=1);

namespace App\Models\Wiki\Song;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Song\Performance\PerformanceCreated;
use App\Events\Wiki\Song\Performance\PerformanceDeleted;
use App\Events\Wiki\Song\Performance\PerformanceDeleting;
use App\Events\Wiki\Song\Performance\PerformanceRestored;
use App\Events\Wiki\Song\Performance\PerformanceUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Database\Factories\Wiki\Song\PerformanceFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property int $performance_id
 * @property string|null $alias
 * @property string|null $as
 * @property int $artist_id
 * @property Artist $artist
 * @property int|null $member_id
 * @property string|null $member_alias
 * @property string|null $member_as
 * @property Artist|null $member
 * @property int $relevance
 * @property int $song_id
 * @property Song $song
 *
 * @method static PerformanceFactory factory(...$parameters)
 */
#[Table(Performance::TABLE, Performance::ATTRIBUTE_ID)]
class Performance extends BaseModel implements Auditable, SoftDeletable, Sortable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;
    use Submitable;

    final public const string TABLE = 'performances';

    final public const string ATTRIBUTE_ID = 'performance_id';
    final public const string ATTRIBUTE_SONG = 'song_id';
    final public const string ATTRIBUTE_ARTIST = 'artist_id';
    final public const string ATTRIBUTE_ALIAS = 'alias';
    final public const string ATTRIBUTE_AS = 'as';
    final public const string ATTRIBUTE_MEMBER = 'member_id';
    final public const string ATTRIBUTE_MEMBER_ALIAS = 'member_alias';
    final public const string ATTRIBUTE_MEMBER_AS = 'member_as';
    final public const string ATTRIBUTE_RELEVANCE = 'relevance';

    final public const string RELATION_ARTIST = 'artist';
    final public const string RELATION_MEMBER = 'member';
    final public const string RELATION_SONG = 'song';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => PerformanceCreated::class,
        'deleted' => PerformanceDeleted::class,
        'deleting' => PerformanceDeleting::class,
        'restored' => PerformanceRestored::class,
        'updated' => PerformanceUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Performance::ATTRIBUTE_SONG,
        Performance::ATTRIBUTE_ARTIST,
        Performance::ATTRIBUTE_ALIAS,
        Performance::ATTRIBUTE_AS,
        Performance::ATTRIBUTE_MEMBER,
        Performance::ATTRIBUTE_MEMBER_ALIAS,
        Performance::ATTRIBUTE_MEMBER_AS,
        Performance::ATTRIBUTE_RELEVANCE,
    ];

    public $sortable = [
        'order_column_name' => Performance::ATTRIBUTE_RELEVANCE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Performance::ATTRIBUTE_ALIAS => 'string',
            Performance::ATTRIBUTE_ARTIST => 'int',
            Performance::ATTRIBUTE_AS => 'string',
            Performance::ATTRIBUTE_MEMBER => 'int',
            Performance::ATTRIBUTE_MEMBER_ALIAS => 'string',
            Performance::ATTRIBUTE_MEMBER_AS => 'string',
            Performance::ATTRIBUTE_RELEVANCE => 'int',
            Performance::ATTRIBUTE_SONG => 'int',
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return $this->song->getName();
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->whereBelongsTo($this->song);
    }

    /**
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, Song::ATTRIBUTE_ID);
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, Performance::ATTRIBUTE_ARTIST);
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Artist::class, Performance::ATTRIBUTE_MEMBER);
    }
}
