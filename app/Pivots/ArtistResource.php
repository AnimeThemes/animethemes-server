<?php

namespace App\Pivots;

use App\Events\Pivot\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\ArtistResource\ArtistResourceUpdated;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArtistResource extends Pivot
{
    /**
     * @var array
     */
    protected $fillable = ['as'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist_resource';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ArtistResourceCreated::class,
        'deleted' => ArtistResourceDeleted::class,
        'updated' => ArtistResourceUpdated::class,
    ];

    /**
     * Gets the artist that owns the artist resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function artist()
    {
        return $this->belongsTo('App\Models\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the resource that owns the artist resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resource()
    {
        return $this->belongsTo('App\Models\ExternalResource', 'resource_id', 'resource_id');
    }
}
