<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Reportable;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use Database\Factories\Wiki\ArtistFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Class Artist.
 *
 * @property int $artist_id
 * @property Collection<int, Artist> $groups
 * @property Collection<int, Image> $images
 * @property string|null $information
 * @property Collection<int, Artist> $members
 * @property string $name
 * @property Collection<int, ExternalResource> $resources
 * @property string $slug
 * @property Collection<int, Song> $songs
 *
 * @method static ArtistFactory factory(...$parameters)
 */
class Artist extends BaseModel implements HasResources, HasImages
{
    use Reportable;
    use Searchable;

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
     * @var array
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
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Artist::ATTRIBUTE_SLUG;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * Get the songs the artist has performed in.
     *
     * @return BelongsToMany<Song, $this>
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
            Performance::class, Membership::class,
            'member_id', 'artist_id'
        )->select(['memberships.as', 'memberships.alias'])
        ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Membership::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, Membership::ATTRIBUTE_MEMBER);
    }

    /**
     * Get the resources for the artist.
     *
     * @return BelongsToMany<ExternalResource, $this>
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(ExternalResource::class, ArtistResource::TABLE, Artist::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_ID)
            ->using(ArtistResource::class)
            ->withPivot(ArtistResource::ATTRIBUTE_AS)
            ->as(ArtistResourceResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the members that comprise this group.
     *
     * @return BelongsToMany<Artist, $this>
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
     * @return BelongsToMany<Artist, $this>
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
     * @return BelongsToMany<Image, $this>
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, ArtistImage::TABLE, Artist::ATTRIBUTE_ID, Image::ATTRIBUTE_ID)
            ->using(ArtistImage::class)
            ->withPivot(ArtistImage::ATTRIBUTE_DEPTH)
            ->as(ArtistImageResource::$wrap)
            ->withTimestamps();
    }
}
