<?php

declare(strict_types=1);

namespace App\Pivots\List;

use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\List\PlaylistImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PlaylistImage.
 *
 * @property Image $image
 * @property int $image_id
 * @property Playlist $playlist
 * @property int $playlist_id
 *
 * @method static PlaylistImageFactory factory(...$parameters)
 */
class PlaylistImage extends BasePivot
{
    final public const TABLE = 'playlist_image';

    final public const ATTRIBUTE_IMAGE = 'image_id';
    final public const ATTRIBUTE_PLAYLIST = 'playlist_id';

    final public const RELATION_IMAGE = 'image';
    final public const RELATION_PLAYLIST = 'playlist';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PlaylistImage::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            PlaylistImage::ATTRIBUTE_IMAGE,
            PlaylistImage::ATTRIBUTE_PLAYLIST,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        PlaylistImage::ATTRIBUTE_IMAGE,
        PlaylistImage::ATTRIBUTE_PLAYLIST,
    ];

    /**
     * Gets the playlist that owns the playlist image.
     *
     * @return BelongsTo<Playlist, $this>
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, PlaylistImage::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Gets the image that owns the playlist image.
     *
     * @return BelongsTo<Image, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, PlaylistImage::ATTRIBUTE_IMAGE);
    }
}
