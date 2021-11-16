<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\Wiki\Streamable;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageDeleting;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Models\BaseModel;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use BenSampo\Enum\Enum;
use Database\Factories\Wiki\ImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Image.
 *
 * @property Collection $anime
 * @property Collection $artists
 * @property Enum|null $facet
 * @property int $image_id
 * @property string $mimetype
 * @property string $path
 * @property int $size
 *
 * @method static ImageFactory factory(...$parameters)
 */
class Image extends BaseModel implements Streamable
{
    public const TABLE = 'images';

    public const ATTRIBUTE_FACET = 'facet';
    public const ATTRIBUTE_ID = 'image_id';
    public const ATTRIBUTE_MIMETYPE = 'mimetype';
    public const ATTRIBUTE_PATH = 'path';
    public const ATTRIBUTE_SIZE = 'size';

    public const RELATION_ANIME = 'anime';
    public const RELATION_ARTISTS = 'artists';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Image::ATTRIBUTE_FACET,
        Image::ATTRIBUTE_MIMETYPE,
        Image::ATTRIBUTE_PATH,
        Image::ATTRIBUTE_SIZE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ImageCreated::class,
        'deleted' => ImageDeleted::class,
        'deleting' => ImageDeleting::class,
        'restored' => ImageRestored::class,
        'updated' => ImageUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Image::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Image::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        Image::ATTRIBUTE_FACET => ImageFacet::class,
        Image::ATTRIBUTE_SIZE => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->path;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get MIME type.
     *
     * @return string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get name of storage disk.
     *
     * @return string
     */
    public function getDisk(): string
    {
        return 'images';
    }

    /**
     * Get the anime that use this image.
     *
     * @return BelongsToMany
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeImage::TABLE, Image::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeImage::class)
            ->withTimestamps();
    }

    /**
     * Get the artists that use this image.
     *
     * @return BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistImage::TABLE, Image::ATTRIBUTE_ID, Artist::ATTRIBUTE_ID)
            ->using(ArtistImage::class)
            ->withTimestamps();
    }
}
