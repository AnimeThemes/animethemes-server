<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageDeleted;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\ArtistImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArtistImage.
 *
 * @property Artist $artist
 * @property int $artist_id
 * @property Image $image
 * @property int $image_id
 *
 * @method static ArtistImageFactory factory(...$parameters)
 */
class ArtistImage extends BasePivot
{
    final public const TABLE = 'artist_image';

    final public const ATTRIBUTE_ARTIST = 'artist_id';
    final public const ATTRIBUTE_IMAGE = 'image_id';

    final public const RELATION_ARTIST = 'artist';
    final public const RELATION_IMAGE = 'image';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ArtistImage::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            ArtistImage::ATTRIBUTE_ARTIST,
            ArtistImage::ATTRIBUTE_IMAGE,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        ArtistImage::ATTRIBUTE_ARTIST,
        ArtistImage::ATTRIBUTE_IMAGE,
    ];

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
