<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\ArtistMemberFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistMember.
 *
 * @property Artist $artist
 * @property string $as
 * @property Artist $member
 *
 * @method static ArtistMemberFactory factory(...$parameters)
 */
class ArtistMember extends BasePivot
{
    final public const TABLE = 'artist_member';

    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_MEMBER = 'member_id';

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
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            ArtistMember::ATTRIBUTE_ARTIST,
            ArtistMember::ATTRIBUTE_MEMBER,
        ];
    }

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
