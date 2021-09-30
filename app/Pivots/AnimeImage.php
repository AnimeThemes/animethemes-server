<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\AnimeImage\AnimeImageDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Database\Factories\Pivots\AnimeImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeImage.
 *
 * @property Anime $anime
 * @property Image $image
 *
 * @method static AnimeImageFactory factory(...$parameters)
 */
class AnimeImage extends BasePivot
{
    public const TABLE = 'anime_image';

    public const ATTRIBUTE_ANIME = 'anime_id';
    public const ATTRIBUTE_IMAGE = 'image_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeImage::TABLE;

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
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeImage::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the image that owns the anime image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, AnimeImage::ATTRIBUTE_IMAGE);
    }
}
