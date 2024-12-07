<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\StudioImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StudioImage.
 *
 * @property Image $image
 * @property int $image_id
 * @property Studio $studio
 * @property int $studio_id
 *
 * @method static StudioImageFactory factory(...$parameters)
 */
class StudioImage extends BasePivot
{
    final public const TABLE = 'studio_image';

    final public const ATTRIBUTE_IMAGE = 'image_id';
    final public const ATTRIBUTE_STUDIO = 'studio_id';

    final public const RELATION_IMAGE = 'image';
    final public const RELATION_STUDIO = 'studio';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = StudioImage::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            StudioImage::ATTRIBUTE_STUDIO,
            StudioImage::ATTRIBUTE_IMAGE,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        StudioImage::ATTRIBUTE_IMAGE,
        StudioImage::ATTRIBUTE_STUDIO,
    ];

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
     * @return BelongsTo<Studio, $this>
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, StudioImage::ATTRIBUTE_STUDIO);
    }

    /**
     * Gets the image that owns the studio image.
     *
     * @return BelongsTo<Image, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, StudioImage::ATTRIBUTE_IMAGE);
    }
}
