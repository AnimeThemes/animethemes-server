<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageDeleting;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Pivots\List\PlaylistImage;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\StudioImage;
use Database\Factories\Wiki\ImageFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Image.
 *
 * @property Collection<int, Anime> $anime
 * @property Collection<int, Artist> $artists
 * @property ImageFacet|null $facet
 * @property int $image_id
 * @property string $mimetype
 * @property string $path
 * @property Collection<int, Playlist> $playlists
 * @property int $size
 * @property Collection<int, Studio> $studios
 *
 * @method static ImageFactory factory(...$parameters)
 */
class Image extends BaseModel
{
    final public const TABLE = 'images';

    final public const ATTRIBUTE_FACET = 'facet';
    final public const ATTRIBUTE_ID = 'image_id';
    final public const ATTRIBUTE_PATH = 'path';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_PLAYLISTS = 'playlists';
    final public const RELATION_STUDIOS = 'studios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        Image::ATTRIBUTE_FACET,
        Image::ATTRIBUTE_PATH,
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Image::ATTRIBUTE_FACET => ImageFacet::class,
        ];
    }

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
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->path;
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
            ->as(AnimeImageResource::$wrap)
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
            ->as(ArtistImageResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the studios that use this image.
     *
     * @return BelongsToMany
     */
    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class, StudioImage::TABLE, Image::ATTRIBUTE_ID, Studio::ATTRIBUTE_ID)
            ->using(StudioImage::class)
            ->as(StudioImageResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the playlists that use this image.
     *
     * @return BelongsToMany
     */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, PlaylistImage::TABLE, Image::ATTRIBUTE_ID, Playlist::ATTRIBUTE_ID)
            ->using(PlaylistImage::class)
            ->as(PlaylistImageResource::$wrap)
            ->withTimestamps();
    }
}
