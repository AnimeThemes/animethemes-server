<?php declare(strict_types=1);

namespace App\Models;

use App\Contracts\Streamable;
use App\Enums\ImageFacet;
use App\Events\Image\ImageCreated;
use App\Events\Image\ImageDeleted;
use App\Events\Image\ImageDeleting;
use App\Events\Image\ImageRestored;
use App\Events\Image\ImageUpdated;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Image
 * @package App\Models
 */
class Image extends BaseModel implements Streamable
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['path', 'facet', 'size', 'mimetype'];

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
    protected $table = 'image';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        'facet' => ImageFacet::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'facet' => 'int',
        'size' => 'int',
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
        return $this->belongsToMany('App\Models\Anime', 'anime_image', 'image_id', 'anime_id')
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
        return $this->belongsToMany('App\Models\Artist', 'artist_image', 'image_id', 'artist_id')
            ->using(ArtistImage::class)
            ->withTimestamps();
    }
}
