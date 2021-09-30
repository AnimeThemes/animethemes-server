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
 * @property Artist $artist
 * @property string $as
 * @property ExternalResource $resource
 * @method static ArtistResourceFactory factory(...$parameters)
 */
class ArtistResource extends BasePivot
{
    public const TABLE = 'artist_resource';

    public const ATTRIBUTE_AS = 'as';
    public const ATTRIBUTE_ARTIST = 'artist_id';
    public const ATTRIBUTE_RESOURCE = 'resource_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ArtistResource::ATTRIBUTE_AS,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ArtistResource::TABLE;

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
        return $this->belongsTo(Artist::class, ArtistResource::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the resource that owns the artist resource.
     *
     * @return BelongsTo
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(ExternalResource::class, ArtistResource::ATTRIBUTE_RESOURCE);
    }
}
