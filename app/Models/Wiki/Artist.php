<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\HasSynonyms;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistForceDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberJsonResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
use App\Scout\Elasticsearch\Models\Wiki\ArtistElasticModel;
use App\Scout\Typesense\Models\Wiki\ArtistTypesenseModel;
use Database\Factories\Wiki\ArtistFactory;
use Deprecated;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property int $artist_id
 * @property Collection<int, Artist> $groups
 * @property Collection<int, Image> $images
 * @property string|null $information
 * @property Collection<int, Artist> $members
 * @property string $name
 * @property Collection<int, Performance> $performances
 * @property Collection<int, ExternalResource> $resources
 * @property string $slug
 * @property Collection<int, Song> $songs
 * @property Collection<int, Synonym> $synonyms
 *
 * @method static ArtistFactory factory(...$parameters)
 */
#[Table(Artist::TABLE, Artist::ATTRIBUTE_ID)]
class Artist extends BaseModel implements Auditable, HasImages, HasResources, HasSynonyms, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use HasRelationships;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'artists';

    final public const string ATTRIBUTE_ID = 'artist_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';
    final public const string ATTRIBUTE_INFORMATION = 'information';

    final public const string RELATION_ANIME = 'songs.animethemes.anime';
    final public const string RELATION_ANIMETHEMES = 'songs.animethemes';
    final public const string RELATION_GROUPS = 'groups';
    final public const string RELATION_IMAGES = 'images';
    final public const string RELATION_MEMBERS = 'members';
    final public const string RELATION_MEMBER_PERFORMANCES = 'memberPerformances';
    final public const string RELATION_PERFORMANCES = 'performances';
    final public const string RELATION_PERFORMANCES_SONGS = 'performances.song';
    final public const string RELATION_RESOURCES = 'resources';
    final public const string RELATION_SONGS = 'songs';
    final public const string RELATION_SYNONYMS = 'synonyms';
    final public const string RELATION_THEME_GROUPS = 'songs.animethemes.group';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ArtistCreated::class,
        'deleted' => ArtistDeleted::class,
        'forceDeleted' => ArtistForceDeleted::class,
        'restored' => ArtistRestored::class,
        'updated' => ArtistUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Artist::ATTRIBUTE_NAME,
        Artist::ATTRIBUTE_SLUG,
        Artist::ATTRIBUTE_INFORMATION,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Artist::ATTRIBUTE_INFORMATION => 'string',
            Artist::ATTRIBUTE_NAME => 'string',
            Artist::ATTRIBUTE_SLUG => 'string',
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            Artist::RELATION_PERFORMANCES,
            Artist::RELATION_MEMBER_PERFORMANCES,
            Artist::RELATION_SYNONYMS,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return match ($driver = Config::get('scout.driver')) {
            'collection',
            'elastic' => ArtistElasticModel::toSearchableArray($this),
            'typesense' => ArtistTypesenseModel::toSearchableArray($this),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Artist::ATTRIBUTE_SLUG;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * @return BelongsToMany<Song, $this, ArtistSong>
     */
    #[Deprecated]
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, ArtistSong::TABLE, ArtistSong::ATTRIBUTE_ARTIST, ArtistSong::ATTRIBUTE_SONG)
            ->using(ArtistSong::class)
            ->withPivot([ArtistSong::ATTRIBUTE_ALIAS, ArtistSong::ATTRIBUTE_AS])
            ->as(ArtistSongJsonResource::$wrap)
            ->withPivot(ArtistSong::ATTRIBUTE_ID)
            ->withTimestamps();
    }

    /**
     * Get the synonyms for the owner model.
     *
     * @return MorphMany<Synonym, $this>
     */
    public function synonyms(): MorphMany
    {
        return $this->morphMany(Synonym::class, Synonym::RELATION_SYNONYMABLE);
    }

    /**
     * @return HasMany<Performance, $this>
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class, Performance::ATTRIBUTE_ARTIST);
    }

    /**
     * @return HasMany<Performance, $this>
     */
    public function memberPerformances(): HasMany
    {
        return $this->hasMany(Performance::class, Performance::ATTRIBUTE_MEMBER);
    }

    /**
     * @return BelongsToMany<Artist, $this, ArtistMember>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER)
            ->using(ArtistMember::class)
            ->as(ArtistMemberJsonResource::$wrap)
            ->withPivot([
                ArtistMember::ATTRIBUTE_ID,
                ArtistMember::ATTRIBUTE_ALIAS,
                ArtistMember::ATTRIBUTE_AS,
                ArtistMember::ATTRIBUTE_NOTES,
                ArtistMember::ATTRIBUTE_RELEVANCE,
            ])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Artist, $this, ArtistMember>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, ArtistMember::ATTRIBUTE_MEMBER, ArtistMember::ATTRIBUTE_ARTIST)
            ->using(ArtistMember::class)
            ->as(ArtistMemberJsonResource::$wrap)
            ->withPivot([
                ArtistMember::ATTRIBUTE_ID,
                ArtistMember::ATTRIBUTE_ALIAS,
                ArtistMember::ATTRIBUTE_AS,
                ArtistMember::ATTRIBUTE_NOTES,
                ArtistMember::ATTRIBUTE_RELEVANCE,
            ])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'artistresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->as('artistresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Image, $this, Imageable, 'artistimage'>
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGEABLE_ID, Imageable::ATTRIBUTE_IMAGE)
            ->using(Imageable::class)
            ->as('artistimage')
            ->withPivot([Imageable::ATTRIBUTE_ID, Imageable::ATTRIBUTE_DEPTH])
            ->withTimestamps();
    }
}
