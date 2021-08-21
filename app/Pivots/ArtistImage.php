<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\ArtistImage\ArtistImageDeleted;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Database\Factories\Pivots\ArtistImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistImage.
 *
 * @property Artist $artist
 * @property Image $image
 * @method static ArtistImageFactory factory(...$parameters)
 */
class ArtistImage extends BasePivot
{
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
        return $this->belongsTo('App\Models\Wiki\Artist', 'artist_id', 'artist_id');
    }

    /**
     * Gets the image that owns the artist image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Image', 'image_id', 'image_id');
    }
}
