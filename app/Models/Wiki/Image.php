<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageDeleting;
use App\Events\Wiki\Image\ImageForceDeleting;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Pivots\Morph\Imageable;
use Database\Factories\Wiki\ImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
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
class Image extends BaseModel implements SoftDeletable
{
    use HasFactory;
    use Reportable;
    use SoftDeletes;

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
     * @var class-string[]
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

    public function getName(): string
    {
        return $this->path;
    }

    public function getSubtitle(): string
    {
        return $this->path;
    }

    /**
     * The link of the image model.
     */
    public function getLinkAttribute(): ?string
    {
        if ($this->hasAttribute(Image::ATTRIBUTE_PATH) && $this->exists) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));

            return $fs->url($this->getAttribute(Image::ATTRIBUTE_PATH));
        }

        return null;
    }

    /**
     * Get the anime that use this image.
     *
     * @return MorphToMany
     */
    public function anime(): MorphToMany
    {
        return $this->morphedByMany(Anime::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('animeimage')
            ->withTimestamps();
    }

    /**
     * Get the artists that use this image.
     *
     * @return MorphToMany
     */
    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('artistimage')
            ->withPivot(Imageable::ATTRIBUTE_DEPTH)
            ->withTimestamps();
    }

    /**
     * Get the studios that use this image.
     *
     * @return MorphToMany
     */
    public function studios(): MorphToMany
    {
        return $this->morphedByMany(Studio::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('studioimage')
            ->withTimestamps();
    }

    /**
     * Get the playlists that use this image.
     *
     * @return MorphToMany
     */
    public function playlists(): MorphToMany
    {
        return $this->morphedByMany(Playlist::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('playlistimage')
            ->withTimestamps();
    }
}
