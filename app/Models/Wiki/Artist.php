<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
use Database\Factories\Wiki\ArtistFactory;
use Deprecated;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property int $artist_id
 * @property Collection<int, Artist> $groups
 * @property Collection<int, Image> $images
 * @property string|null $information
 * @property Collection<int, Artist> $members
 * @property Collection<int, Membership> $memberships
 * @property string $name
 * @property Collection<int, Performance> $performances
 * @property Collection<int, ExternalResource> $resources
 * @property string $slug
 * @property Collection<int, Song> $songs
 *
 * @method static ArtistFactory factory(...$parameters)
 */
class Artist extends BaseModel implements Auditable, HasImages, HasResources, SoftDeletable
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
    final public const string RELATION_GROUPSHIPS = 'groupships';
    final public const string RELATION_GROUP_PERFORMANCES = 'groupperformances';
    final public const string RELATION_GROUPSHIPS_PERFORMANCES = 'groupships.performances';
    final public const string RELATION_IMAGES = 'images';
    final public const string RELATION_MEMBERS = 'members';
    final public const string RELATION_MEMBERSHIPS = 'memberships';
    final public const string RELATION_MEMBERSHIPS_PERFORMANCES = 'memberships.performances';
    final public const string RELATION_MEMBERSHIPS_PERFORMANCES_SONGS = 'memberships.performances.song';
    final public const string RELATION_PERFORMANCES = 'performances';
    final public const string RELATION_PERFORMANCES_SONGS = 'performances.song';
    final public const string RELATION_RESOURCES = 'resources';
    final public const string RELATION_SONGS = 'songs';
    final public const string RELATION_THEME_GROUPS = 'songs.animethemes.group';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Artist::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Artist::ATTRIBUTE_ID;

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
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            Artist::RELATION_PERFORMANCES_SONGS,
            Artist::RELATION_MEMBERSHIPS_PERFORMANCES_SONGS,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $directPerformances = $this->performances->map(fn (Performance $performance) => $performance->toArray());

        $membershipPerformances = $this->memberships->flatMap(
            fn (Membership $membership) => $membership->performances->map(fn (Performance $performance): array => array_merge(
                $performance->toArray(),
                ['membership_alias' => $membership->alias],
                ['membership_as' => $membership->as],
            ))
        );

        $array['performances'] = $directPerformances->concat($membershipPerformances)->all();

        return $array;
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
            ->as(ArtistSongResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return MorphMany<Performance, $this>
     */
    public function performances(): MorphMany
    {
        return $this->morphMany(Performance::class, Performance::RELATION_ARTIST);
    }

    /**
     * Relation for filament.
     * Groups performances of the memberships of this group.
     */
    public function groupperformances(): HasManyDeep
    {
        $sub = Performance::query()
            ->selectRaw('MAX(performance_id) as performance_id')
            ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Membership::class))
            ->groupBy(Performance::ATTRIBUTE_SONG);

        return $this->hasManyDeep(
            Performance::class,
            [Membership::class],
            [Membership::ATTRIBUTE_ARTIST, [Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID]],
            [Artist::ATTRIBUTE_ID, Membership::ATTRIBUTE_ID]
        )
            ->joinSub($sub, 'latest_performance', function ($join): void {
                $join->on(new Performance()->qualifyColumn(Performance::ATTRIBUTE_ID), '=', 'latest_performance.performance_id');
            })
            ->where(new Performance()->qualifyColumn(Performance::ATTRIBUTE_ARTIST_TYPE), Relation::getMorphAlias(Membership::class));
    }

    /**
     * The memberships of the member.
     *
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, Membership::ATTRIBUTE_MEMBER);
    }

    /**
     * The memberships of the group.
     *
     * @return HasMany<Membership, $this>
     */
    public function groupships(): HasMany
    {
        return $this->hasMany(Membership::class, Membership::ATTRIBUTE_ARTIST);
    }

    /**
     * @return BelongsToMany<Artist, $this, ArtistMember>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, ArtistMember::ATTRIBUTE_ARTIST, ArtistMember::ATTRIBUTE_MEMBER)
            ->using(ArtistMember::class)
            ->withPivot([ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS, ArtistMember::ATTRIBUTE_NOTES, ArtistMember::ATTRIBUTE_RELEVANCE])
            ->as(ArtistMemberResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Artist, $this, ArtistMember>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, ArtistMember::ATTRIBUTE_MEMBER, ArtistMember::ATTRIBUTE_ARTIST)
            ->using(ArtistMember::class)
            ->withPivot([ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS, ArtistMember::ATTRIBUTE_NOTES])
            ->as(ArtistMemberResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'artistresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('artistresource')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Image, $this, Imageable, 'artistimage'>
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGEABLE_ID, Imageable::ATTRIBUTE_IMAGE)
            ->using(Imageable::class)
            ->withPivot(Imageable::ATTRIBUTE_DEPTH)
            ->as('artistimage')
            ->withTimestamps();
    }
}
