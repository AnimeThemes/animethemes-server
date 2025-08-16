<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

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
 * @property int|null $depth
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
    final public const ATTRIBUTE_DEPTH = 'depth';

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
     * @var list<string>
     */
    protected $fillable = [
        ArtistImage::ATTRIBUTE_ARTIST,
        ArtistImage::ATTRIBUTE_IMAGE,
        ArtistImage::ATTRIBUTE_DEPTH,
    ];

    /**
     * Gets the artist that owns the artist image.
     *
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, ArtistImage::ATTRIBUTE_ARTIST);
    }

    /**
     * Gets the image that owns the artist image.
     *
     * @return BelongsTo<Image, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, ArtistImage::ATTRIBUTE_IMAGE);
    }
}
