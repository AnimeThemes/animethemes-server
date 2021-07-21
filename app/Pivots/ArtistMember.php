<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use Database\Factories\Pivots\ArtistMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistMember.
 *
 * @property string $as
 * @property Artist $artist
 * @property Artist $member
 * @method static ArtistMemberFactory factory(...$parameters)
 */
class ArtistMember extends BasePivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['as'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist_member';

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
        return $this->belongsTo('App\Models\Wiki\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the member that owns the artist member.
     *
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Artist', 'member_id', 'artist_id');
    }
}
