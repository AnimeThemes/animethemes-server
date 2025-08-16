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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * Class Artist.
 *
 * @property int $artist_id
 * @property Collection<int, Artist> $groups
 * @property Collection<int, Image> $images
 * @property string|null $information
 * @property Collection<int, Artist> $members
 * @property Collection<int, Membership> $memberships
 * @property string $name
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
    final public const RELATION_IMAGES = 'images';
    final public const RELATION_MEMBERS = 'members';
    final public const RELATION_MEMBERSHIPS = 'memberships';
    final public const RELATION_PERFORMANCES = 'performances';
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
        return $query->with(Artist::RELATION_SONGS);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['songs'] = $this->songs->toArray();

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

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * Get the songs the artist has performed in.
     *
     * @return BelongsToMany
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, ArtistSong::TABLE, Artist::ATTRIBUTE_ID, Song::ATTRIBUTE_ID)
            ->using(ArtistSong::class)
            ->withPivot([ArtistSong::ATTRIBUTE_ALIAS, ArtistSong::ATTRIBUTE_AS])
            ->as(ArtistSongResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the performances of the artist.
     *
     * @return MorphMany
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
            ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Membership::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, Membership::ATTRIBUTE_MEMBER);
    }

    /**
     * Get the resources for the artist through the resourceable morph pivot.
     *
     * @return MorphToMany
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
     * Get the members that comprise this group.
     *
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, Artist::ATTRIBUTE_ID, 'member_id')
            ->using(ArtistMember::class)
            ->withPivot([ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS, ArtistMember::ATTRIBUTE_NOTES])
            ->as(ArtistMemberResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the groups the artist has performed in.
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistMember::TABLE, 'member_id', Artist::ATTRIBUTE_ID)
            ->using(ArtistMember::class)
            ->withPivot([ArtistMember::ATTRIBUTE_ALIAS, ArtistMember::ATTRIBUTE_AS, ArtistMember::ATTRIBUTE_NOTES])
            ->as(ArtistMemberResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the images for the artist.
     *
     * @return MorphToMany
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
