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
use Illuminate\Database\Eloquent\Attributes\Table;
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
 * @property Uri $link
 * @property int $resource_id
 * @property ResourceSite $site
 * @property Collection<int, Song> $songs
 * @property Collection<int, Studio> $studios
 *
 * @method static ExternalResourceFactory factory(...$parameters)
 */
#[Table(ExternalResource::TABLE, ExternalResource::ATTRIBUTE_ID)]
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
            ->as('animeresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<AnimeThemeEntry, $this, Resourceable, 'entryresource'>
     */
    public function animethemeentries(): MorphToMany
    {
        return $this->morphedByMany(AnimeThemeEntry::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->as('entryresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Artist, $this, Resourceable, 'artistresource'>
     */
    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->as('artistresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Song, $this, Resourceable, 'songresource'>
     */
    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->as('songresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Studio, $this, Resourceable, 'studioresource'>
     */
    public function studios(): MorphToMany
    {
        return $this->morphedByMany(Studio::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID)
            ->using(Resourceable::class)
            ->as('studioresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }
}
