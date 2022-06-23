<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\StudioImage\StudioImageCreated;
use App\Events\Pivot\StudioImage\StudioImageDeleted;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Database\Factories\Pivots\StudioImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StudioImage.
 *
 * @property Studio $studio
 * @property Image $image
 *
 * @method static StudioImageFactory factory(...$parameters)
 */
class StudioImage extends BasePivot
{
    final public const TABLE = 'studio_image';

    final public const ATTRIBUTE_STUDIO = 'studio_id';
    final public const ATTRIBUTE_IMAGE = 'image_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = StudioImage::TABLE;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => StudioImageCreated::class,
        'deleted' => StudioImageDeleted::class,
    ];

    /**
     * Gets the studio that owns the studio image.
     *
     * @return BelongsTo
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, StudioImage::ATTRIBUTE_STUDIO);
    }

    /**
     * Gets the image that owns the studio image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, StudioImage::ATTRIBUTE_IMAGE);
    }
}
