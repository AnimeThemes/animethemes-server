<?php

declare(strict_types=1);

namespace App\Models\Wiki\Song;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Performance.
 *
 * @property int $performance_id
 * @property string|null $alias
 * @property string|null $as
 * @property string $artist_type
 * @property int $artist_id
 * @property Artist|Membership $artist
 * @property Song $song
 *
 * @method static PerformanceFactory factory(...$parameters)
 */
class Performance extends BaseModel implements SoftDeletable
{
    use HasFactory;
    use Reportable;
    use SoftDeletes;

    final public const TABLE = 'performances';

    final public const ATTRIBUTE_ID = 'performance_id';
    final public const ATTRIBUTE_SONG = 'song_id';
    final public const ATTRIBUTE_ARTIST_TYPE = 'artist_type';
    final public const ATTRIBUTE_ARTIST_ID = 'artist_id';
    final public const ATTRIBUTE_ARTIST = 'artist';
    final public const ATTRIBUTE_ALIAS = 'alias';
    final public const ATTRIBUTE_AS = 'as';

    final public const RELATION_ARTIST = 'artist';
    final public const RELATION_MEMBERSHIP = self::RELATION_ARTIST;
    final public const RELATION_SONG = 'song';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Performance::ATTRIBUTE_SONG,
        Performance::ATTRIBUTE_ARTIST_TYPE,
        Performance::ATTRIBUTE_ARTIST_ID,
        Performance::ATTRIBUTE_ALIAS,
        Performance::ATTRIBUTE_AS,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => PerformanceCreated::class,
        'deleted' => PerformanceDeleted::class,
        'deleting' => PerformanceDeleting::class,
        'restored' => PerformanceRestored::class,
        'updated' => PerformanceUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Performance::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Performance::ATTRIBUTE_ID;

    /**
     * Get name.
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        $this->loadMissing([
            Performance::RELATION_SONG,
        ]);

        return $this->song->getName();
    }

    /**
     * Get the eager loads needed to the subtitle.
     *
     * @return string[]
     */
    public static function getEagerLoadsForSubtitle(): array
    {
        return [
            Performance::RELATION_SONG,
        ];
    }

    /**
     * Determine if the performance is a membership.
     *
     * @return bool
     */
    public function isMembership(): bool
    {
        return $this->artist_type === Membership::class;
    }

    /**
     * Get the song that owns the performance.
     *
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, Song::ATTRIBUTE_ID);
    }

    /**
     * Get the artist that owns the performance.
     *
     * @return MorphTo
     */
    public function artist(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Alias of artist().
     *
     * @return MorphTo
     */
    public function membership(): MorphTo
    {
        return $this->artist();
    }
}
