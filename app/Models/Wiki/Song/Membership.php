<?php

declare(strict_types=1);

namespace App\Models\Wiki\Song;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Song\Membership\MembershipCreated;
use App\Events\Wiki\Song\Membership\MembershipDeleted;
use App\Events\Wiki\Song\Membership\MembershipDeleting;
use App\Events\Wiki\Song\Membership\MembershipRestored;
use App\Events\Wiki\Song\Membership\MembershipUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Artist;
use Database\Factories\Wiki\Song\MembershipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property int $membership_id
 * @property string|null $alias
 * @property string|null $as
 * @property int $artist_id
 * @property Artist $group
 * @property int $member_id
 * @property Artist $member
 * @property Collection<int, Performance> $performances
 *
 * @method static MembershipFactory factory(...$parameters)
 */
class Membership extends BaseModel implements SoftDeletable
{
    use HasFactory;
    use Reportable;
    use SoftDeletes;

    final public const TABLE = 'memberships';

    final public const ATTRIBUTE_ID = 'membership_id';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_ALIAS = 'alias';
    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_MEMBER = 'member_id';

    final public const RELATION_GROUP = 'group';
    final public const RELATION_MEMBER = 'member';
    final public const RELATION_PERFORMANCES = 'performances';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Membership::ATTRIBUTE_ALIAS,
        Membership::ATTRIBUTE_ARTIST,
        Membership::ATTRIBUTE_AS,
        Membership::ATTRIBUTE_MEMBER,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => MembershipCreated::class,
        'deleted' => MembershipDeleted::class,
        'deleting' => MembershipDeleting::class,
        'restored' => MembershipRestored::class,
        'updated' => MembershipUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Membership::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Membership::ATTRIBUTE_ID;

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return "Member {$this->member->getName()} of Group {$this->group->getName()}";
    }

    /**
     * Get the group that owns the membership.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Artist::class, Membership::ATTRIBUTE_ARTIST);
    }

    /**
     * Get the member that owns the membership.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Artist::class, Membership::ATTRIBUTE_MEMBER);
    }

    /**
     * Get the performances of the membership.
     *
     * @return MorphMany
     */
    public function performances(): MorphMany
    {
        return $this->morphMany(Performance::class, Performance::RELATION_ARTIST);
    }
}
