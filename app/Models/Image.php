<?php

namespace App\Models;

use App\Enums\ImageFacet;
use App\Events\Image\ImageCreated;
use App\Events\Image\ImageDeleted;
use App\Events\Image\ImageDeleting;
use App\Events\Image\ImageRestored;
use App\Events\Image\ImageUpdated;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use BenSampo\Enum\Traits\CastsEnums;

class Image extends BaseModel
{
    use CastsEnums;

    /**
     * @var array
     */
    protected $fillable = ['path', 'facet'];

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
     * @var array
     */
    protected $enumCasts = [
        'facet' => ImageFacet::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'facet' => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->path;
    }

    /**
     * Get the anime that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_image', 'image_id', 'anime_id')->using(AnimeImage::class);
    }

    /**
     * Get the artists that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_image', 'image_id', 'artist_id')->using(ArtistImage::class);
    }
}
