<?php

namespace App\Pivots;

use App\Events\Pivot\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\AnimeImage\AnimeImageDeleted;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AnimeImage extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_image';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnimeImageCreated::class,
        'deleted' => AnimeImageDeleted::class,
    ];

    /**
     * Gets the anime that owns the anime image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anime()
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the image that owns the anime image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('App\Models\Image', 'image_id', 'image_id');
    }
}
