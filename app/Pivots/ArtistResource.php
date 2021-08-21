<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\ArtistResource\ArtistResourceUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Database\Factories\Pivots\ArtistResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistResource.
 *
 * @property string $as
 * @property Artist $artist
 * @property ExternalResource $resource
 * @method static ArtistResourceFactory factory(...$parameters)
 */
class ArtistResource extends BasePivot
{
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
     * @return BelongsTo
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the resource that owns the artist resource.
     *
     * @return BelongsTo
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\ExternalResource', 'resource_id', 'resource_id');
    }
}
