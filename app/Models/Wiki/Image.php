<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
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
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

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
class Image extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'images';

    final public const string ATTRIBUTE_FACET = 'facet';
    final public const string ATTRIBUTE_ID = 'image_id';
    final public const string ATTRIBUTE_PATH = 'path';
    final public const string ATTRIBUTE_LINK = 'link';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_ARTISTS = 'artists';
    final public const string RELATION_PLAYLISTS = 'playlists';
    final public const string RELATION_STUDIOS = 'studios';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ImageCreated::class,
        'deleted' => ImageDeleted::class,
        'forceDeleting' => ImageForceDeleting::class,
        'restored' => ImageRestored::class,
        'updated' => ImageUpdated::class,
    ];

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

    protected function getLinkAttribute(): ?string
    {
        if ($this->hasAttribute(Image::ATTRIBUTE_PATH) && $this->exists) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get('image.disk'));

            return $fs->url($this->getAttribute(Image::ATTRIBUTE_PATH));
        }

        return null;
    }

    /**
     * @return MorphToMany<Anime, $this, Imageable, 'animeimage'>
     */
    public function anime(): MorphToMany
    {
        return $this->morphedByMany(Anime::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('animeimage')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Artist, $this, Imageable, 'artistimage'>
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
     * @return MorphToMany<Studio, $this, Imageable, 'studioimage'>
     */
    public function studios(): MorphToMany
    {
        return $this->morphedByMany(Studio::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('studioimage')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Playlist, $this, Imageable, 'playlistimage'>
     */
    public function playlists(): MorphToMany
    {
        return $this->morphedByMany(Playlist::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGE, Imageable::ATTRIBUTE_IMAGEABLE_ID)
            ->using(Imageable::class)
            ->as('playlistimage')
            ->withTimestamps();
    }
}
