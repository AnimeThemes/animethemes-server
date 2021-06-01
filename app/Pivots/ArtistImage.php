<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\ArtistImage\ArtistImageDeleted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistImage.
 */
class ArtistImage extends BasePivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist_image';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ArtistImageCreated::class,
        'deleted' => ArtistImageDeleted::class,
    ];

    /**
     * Gets the artist that owns the artist image.
     *
     * @return BelongsTo
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo('App\Models\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the image that owns the artist image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo('App\Models\Image', 'image_id', 'image_id');
    }
}
