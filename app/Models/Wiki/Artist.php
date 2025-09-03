<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
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
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

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
class Artist extends BaseModel implements HasImages, HasResources, SoftDeletable
{
    use HasFactory;
    use Reportable;
    use Searchable;
    use SoftDeletes;

    final public const TABLE = 'artists';

    final public const ATTRIBUTE_ID = 'artist_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';
    final public const ATTRIBUTE_INFORMATION = 'information';

    final public const RELATION_ANIME = 'songs.animethemes.anime';
    final public const RELATION_ANIMETHEMES = 'songs.animethemes';
    final public const RELATION_GROUPS = 'groups';
    final public const RELATION_GROUP_PERFORMANCES = 'groupperformances';
    final public const RELATION_GROUPMEMBERSHIPS_PERFORMANCES = 'groupmemberships.performances';
    final public const RELATION_IMAGES = 'images';
    final public const RELATION_MEMBERS = 'members';
    final public const RELATION_MEMBERSHIPS = 'memberships';
    final public const RELATION_MEMBERSHIPS_PERFORMANCES = 'memberships.performances';
    final public const RELATION_MEMBERSHIPS_PERFORMANCES_SONGS = 'memberships.performances.song';
    final public const RELATION_PERFORMANCES = 'performances';
    final public const RELATION_PERFORMANCES_SONGS = 'performances.song';
    final public const RELATION_RESOURCES = 'resources';
    final public const RELATION_SONGS = 'songs';
    final public const RELATION_THEME_GROUPS = 'songs.animethemes.group';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => ArtistCreated::class,
        'deleted' => ArtistDeleted::class,
        'restored' => ArtistRestored::class,
        'updated' => ArtistUpdated::class,
    ];

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
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
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
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $directPerformances = $this->performances->map(fn (Performance $performance) => $performance->toArray());

        $membershipPerformances = $this->memberships->flatMap(
            fn (Membership $membership) => $membership->performances->map(fn (Performance $performance) => array_merge(
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

    public function groupperformances()
    {
        return $this->hasManyThrough(
            Performance::class,
            Membership::class,
            'member_id',
            'artist_id'
        )->select(['memberships.as', 'memberships.alias'])
            ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Membership::class));
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
    public function groupmemberships(): HasMany
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
            ->withPivot([ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS, ArtistMember::ATTRIBUTE_NOTES])
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
