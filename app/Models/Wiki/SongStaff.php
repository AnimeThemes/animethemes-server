<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\SongStaff\SongStaffCreated;
use App\Events\Wiki\SongStaff\SongStaffDeleted;
use App\Events\Wiki\SongStaff\SongStaffDeleting;
use App\Events\Wiki\SongStaff\SongStaffRestored;
use App\Events\Wiki\SongStaff\SongStaffUpdated;
use App\Models\BaseModel;
use Database\Factories\Wiki\SongStaffFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property int $id
 * @property string|null $alias
 * @property string|null $as
 * @property int $artist_id
 * @property Artist $artist
 * @property int|null $member_id
 * @property string|null $member_alias
 * @property string|null $member_as
 * @property Artist|null $member
 * @property int $relevance
 * @property string $role
 * @property int $song_id
 * @property Song $song
 *
 * @method static SongStaffFactory factory(...$parameters)
 */
#[Table(SongStaff::TABLE, SongStaff::ATTRIBUTE_ID)]
class SongStaff extends BaseModel implements Auditable, SoftDeletable, Sortable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    final public const string TABLE = 'song_staff';

    final public const string ATTRIBUTE_ID = 'id';

    final public const string ATTRIBUTE_SONG = 'song_id';

    final public const string ATTRIBUTE_ARTIST = 'artist_id';

    final public const string ATTRIBUTE_ALIAS = 'alias';

    final public const string ATTRIBUTE_AS = 'as';

    final public const string ATTRIBUTE_MEMBER = 'member_id';

    final public const string ATTRIBUTE_MEMBER_ALIAS = 'member_alias';

    final public const string ATTRIBUTE_MEMBER_AS = 'member_as';

    final public const string ATTRIBUTE_RELEVANCE = 'relevance';

    final public const string ATTRIBUTE_ROLE = 'role';

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
        'created' => SongStaffCreated::class,
        'deleted' => SongStaffDeleted::class,
        'deleting' => SongStaffDeleting::class,
        'restored' => SongStaffRestored::class,
        'updated' => SongStaffUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        SongStaff::ATTRIBUTE_SONG,
        SongStaff::ATTRIBUTE_ARTIST,
        SongStaff::ATTRIBUTE_ALIAS,
        SongStaff::ATTRIBUTE_AS,
        SongStaff::ATTRIBUTE_MEMBER,
        SongStaff::ATTRIBUTE_MEMBER_ALIAS,
        SongStaff::ATTRIBUTE_MEMBER_AS,
        SongStaff::ATTRIBUTE_RELEVANCE,
        SongStaff::ATTRIBUTE_ROLE,
    ];

    public $sortable = [
        'order_column_name' => SongStaff::ATTRIBUTE_RELEVANCE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            SongStaff::ATTRIBUTE_ALIAS => 'string',
            SongStaff::ATTRIBUTE_ARTIST => 'int',
            SongStaff::ATTRIBUTE_AS => 'string',
            SongStaff::ATTRIBUTE_MEMBER => 'int',
            SongStaff::ATTRIBUTE_MEMBER_ALIAS => 'string',
            SongStaff::ATTRIBUTE_MEMBER_AS => 'string',
            SongStaff::ATTRIBUTE_RELEVANCE => 'int',
            SongStaff::ATTRIBUTE_SONG => 'int',
            SongStaff::ATTRIBUTE_ROLE => 'string',
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
        return $this->belongsTo(Artist::class, SongStaff::ATTRIBUTE_ARTIST);
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Artist::class, SongStaff::ATTRIBUTE_MEMBER);
    }
}
