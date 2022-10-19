<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\Wiki\ArtistResource\ArtistResourceUpdated;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\ArtistResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistResource.
 *
 * @property Artist $artist
 * @property string $as
 * @property ExternalResource $resource
 *
 * @method static ArtistResourceFactory factory(...$parameters)
 */
class ArtistResource extends BasePivot
{
    final public const TABLE = 'artist_resource';

    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_RESOURCE = 'resource_id';

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
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            ArtistResource::ATTRIBUTE_ARTIST,
            ArtistResource::ATTRIBUTE_RESOURCE,
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
