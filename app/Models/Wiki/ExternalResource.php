<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
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
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

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
class ExternalResource extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'resources';

    final public const string ATTRIBUTE_EXTERNAL_ID = 'external_id';
    final public const string ATTRIBUTE_ID = 'resource_id';
    final public const string ATTRIBUTE_LINK = 'link';
    final public const string ATTRIBUTE_SITE = 'site';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_ANIMETHEMEENTRIES = 'animethemeentries';
    final public const string RELATION_ARTISTS = 'artists';
    final public const string RELATION_SONGS = 'songs';
    final public const string RELATION_STUDIOS = 'studios';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ExternalResourceCreated::class,
        'deleted' => ExternalResourceDeleted::class,
        'restored' => ExternalResourceRestored::class,
        'updated' => ExternalResourceUpdated::class,
    ];

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
     * @return MorphToMany<Anime, $this, Resourceable, 'animeresource'>
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
     * @return MorphToMany<AnimeThemeEntry, $this, Resourceable, 'entryresource'>
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
     * @return MorphToMany<Artist, $this, Resourceable, 'artistresource'>
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
     * @return MorphToMany<Song, $this, Resourceable, 'songresource'>
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
     * @return MorphToMany<Studio, $this, Resourceable, 'studioresource'>
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
