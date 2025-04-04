<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageDeleting;
use App\Events\Wiki\Image\ImageForceDeleting;
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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class Image.
 *
 * @property Collection<int, Anime> $anime
 * @property Collection<int, Artist> $artists
 * @property ImageFacet|null $facet
 * @property int $image_id
 * @property string $link
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
    use Reportable;

    final public const TABLE = 'images';

    final public const ATTRIBUTE_FACET = 'facet';
    final public const ATTRIBUTE_ID = 'image_id';
    final public const ATTRIBUTE_PATH = 'path';
    final public const ATTRIBUTE_LINK = 'link';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_PLAYLISTS = 'playlists';
    final public const RELATION_STUDIOS = 'studios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
        'forceDeleting' => ImageForceDeleting::class,
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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Image::ATTRIBUTE_LINK,
    ];

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
     * The link of the image model.
     *
     * @return string|null
     */
    public function getLinkAttribute(): ?string
    {
        if ($this->path) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));

            return $fs->url($this->getAttribute(Image::ATTRIBUTE_PATH));
        }

        return null;
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
            ->withPivot(ArtistImage::ATTRIBUTE_DEPTH)
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
