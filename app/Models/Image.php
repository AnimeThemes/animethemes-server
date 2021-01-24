<?php

namespace App\Models;

use App\Enums\ImageFacet;
use App\Events\Image\ImageCreated;
use App\Events\Image\ImageDeleted;
use App\Events\Image\ImageUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Image extends Model implements Auditable
{
    use CastsEnums, HasFactory;
    use \OwenIt\Auditing\Auditable;

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
     * Get the anime that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_image', 'image_id', 'anime_id');
    }

    /**
     * Get the artists that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_image', 'image_id', 'artist_id');
    }
}
