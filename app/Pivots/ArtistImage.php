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
 *
 * @method static ArtistImageFactory factory(...$parameters)
 */
class ArtistImage extends BasePivot
{
    final public const TABLE = 'artist_image';

    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_IMAGE = 'image_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ArtistImage::TABLE;

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
        return $this->belongsTo(Artist::class, ArtistImage::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the image that owns the artist image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, ArtistImage::ATTRIBUTE_IMAGE);
    }
}
