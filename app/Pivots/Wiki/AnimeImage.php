<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\AnimeImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeImage.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property Image $image
 * @property int $image_id
 *
 * @method static AnimeImageFactory factory(...$parameters)
 */
class AnimeImage extends BasePivot
{
    final public const TABLE = 'anime_image';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_IMAGE = 'image_id';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_IMAGE = 'image';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeImage::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            AnimeImage::ATTRIBUTE_ANIME,
            AnimeImage::ATTRIBUTE_IMAGE,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        AnimeImage::ATTRIBUTE_ANIME,
        AnimeImage::ATTRIBUTE_IMAGE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => AnimeImageCreated::class,
        'deleted' => AnimeImageDeleted::class,
    ];

    /**
     * Gets the anime that owns the anime image.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeImage::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the image that owns the anime image.
     *
     * @return BelongsTo<Image, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, AnimeImage::ATTRIBUTE_IMAGE);
    }
}
