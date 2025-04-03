<?php

declare(strict_types=1);

namespace App\Models\Wiki\Song;

use App\Events\Wiki\Song\Membership\MembershipCreated;
use App\Events\Wiki\Song\Membership\MembershipDeleted;
use App\Events\Wiki\Song\Membership\MembershipDeleting;
use App\Events\Wiki\Song\Membership\MembershipRestored;
use App\Events\Wiki\Song\Membership\MembershipUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Membership.
 *
 * @property int $membership_id
 * @property string|null $alias
 * @property string|null $as
 * @property int $artist_id
 * @property Artist $artist
 * @property int $member_id
 * @property Artist $member
 */
class Membership extends BaseModel
{
    final public const TABLE = 'memberships';

    final public const ATTRIBUTE_ID = 'membership_id';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_ALIAS = 'alias';
    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_MEMBER = 'member_id';

    final public const RELATION_ARTIST = 'artist';
    final public const RELATION_MEMBER = 'member';

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
     * @var array
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

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        $this->loadMissing([
            Membership::RELATION_ARTIST,
            Membership::RELATION_MEMBER,
        ]);

        return "Member {$this->member->getName()} of Group {$this->artist->getName()}";
    }

    /**
     * Get the artist that owns the membership.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
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
