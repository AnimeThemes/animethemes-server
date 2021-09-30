<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use Database\Factories\Pivots\ArtistMemberFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistMember.
 *
 * @property Artist $artist
 * @property string $as
 * @property Artist $member
 * @method static ArtistMemberFactory factory(...$parameters)
 */
class ArtistMember extends BasePivot
{
    public const TABLE = 'artist_member';

    public const ATTRIBUTE_AS = 'as';
    public const ATTRIBUTE_ARTIST = 'artist_id';
    public const ATTRIBUTE_MEMBER = 'member_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ArtistMember::ATTRIBUTE_AS,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ArtistMember::TABLE;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ArtistMemberCreated::class,
        'deleted' => ArtistMemberDeleted::class,
        'updated' => ArtistMemberUpdated::class,
    ];

    /**
     * Gets the artist that owns the artist member.
     *
     * @return BelongsTo
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistMember::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the member that owns the artist member.
     *
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistMember::ATTRIBUTE_MEMBER);
    }
}
