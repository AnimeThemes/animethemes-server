<?php

namespace App\Pivots;

use App\Events\Pivot\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArtistMember extends Pivot
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['as'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist_member';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function artist()
    {
        return $this->belongsTo('App\Models\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the member that owns the artist member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Artist', 'member_id', 'artist_id');
    }
}
