<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ResourceSite;
use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\Morph\Resourceable;
use Database\Factories\Wiki\ExternalResourceFactory;
use Illuminate\Database\Eloquent\Casts\AsUri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Uri;

/**
 * @property Collection<int, Anime> $anime
 * @property Collection<int, Artist> $artists
 * @property int|null $external_id
 * @property Uri|null $link
 * @property int $resource_id
 * @property ResourceSite|null $site
 * @property Collection<int, Song> $songs
 * @property Collection<int, Studio> $studios
 *
 * @method static ExternalResourceFactory factory(...$parameters)
 */
class ExternalResource extends BaseModel implements SoftDeletable
{
    use HasFactory;
    use Reportable;
    use SoftDeletes;

    final public const TABLE = 'resources';

    final public const ATTRIBUTE_EXTERNAL_ID = 'external_id';
    final public const ATTRIBUTE_ID = 'resource_id';
    final public const ATTRIBUTE_LINK = 'link';
    final public const ATTRIBUTE_SITE = 'site';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_ANIMETHEMEENTRIES = 'animethemeentries';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_SONGS = 'songs';
    final public const RELATION_STUDIOS = 'studios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ExternalResource::ATTRIBUTE_EXTERNAL_ID,
        ExternalResource::ATTRIBUTE_LINK,
        ExternalResource::ATTRIBUTE_SITE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => ExternalResourceCreated::class,
        'deleted' => ExternalResourceDeleted::class,
        'restored' => ExternalResourceRestored::class,
        'updated' => ExternalResourceUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ExternalResource::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ExternalResource::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => 'int',
            ExternalResource::ATTRIBUTE_LINK => AsUri::class,
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::class,
        ];
    }

    public function getName(): string
    {
        return strval($this->link);
    }

    public function getSubtitle(): string
    {
        return strval($this->external_id);
    }

    /**
     * Get the anime that reference this resource.
     *
     * @return MorphToMany
     */
    public function anime(): MorphToMany
    {
        return $this->morphedByMany(Anime::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('animeresource')
            ->withTimestamps();
    }

    /**
     * Get the entry that reference this resource.
     *
     * @return MorphToMany
     */
    public function animethemeentries(): MorphToMany
    {
        return $this->morphedByMany(AnimeThemeEntry::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('entryresource')
            ->withTimestamps();
    }

    /**
     * Get the artists that reference this resource.
     *
     * @return MorphToMany
     */
    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('artistresource')
            ->withTimestamps();
    }

    /**
     * Get the songs that reference this resource.
     *
     * @return MorphToMany
     */
    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('songresource')
            ->withTimestamps();
    }

    /**
     * Get the studios that reference this resource.
     *
     * @return MorphToMany
     */
    public function studios(): MorphToMany
    {
        return $this->morphedByMany(Studio::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('studioresource')
            ->withTimestamps();
    }
}
