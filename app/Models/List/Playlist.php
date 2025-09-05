<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Concerns\Models\Aggregate\AggregatesLike;
use App\Concerns\Models\InteractsWithLikes;
use App\Contracts\Models\HasAggregateLikes;
use App\Contracts\Models\HasHashids;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\Likeable;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Database\Factories\List\PlaylistFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * @property string|null $description
 * @property PlaylistTrack|null $first
 * @property int $first_id
 * @property Collection<int, Image> $images
 * @property PlaylistTrack|null $last
 * @property int $last_id
 * @property int $playlist_id
 * @property Collection<int, PlaylistTrack> $tracks
 * @property string $name
 * @property User|null $user
 * @property int|null $user_id
 * @property PlaylistVisibility $visibility
 *
 * @method static PlaylistFactory factory(...$parameters)
 */
class Playlist extends BaseModel implements HasAggregateLikes, HasHashids, HasImages, Likeable
{
    use AggregatesLike;
    use HasFactory;
    use InteractsWithLikes;
    use Searchable;

    final public const TABLE = 'playlists';

    final public const ATTRIBUTE_DESCRIPTION = 'description';
    final public const ATTRIBUTE_FIRST = 'first_id';
    final public const ATTRIBUTE_ID = 'playlist_id';
    final public const ATTRIBUTE_LAST = 'last_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_USER = 'user_id';
    final public const ATTRIBUTE_VISIBILITY = 'visibility';

    final public const RELATION_FIRST = 'first';
    final public const RELATION_IMAGES = 'images';
    final public const RELATION_LAST = 'last';
    final public const RELATION_TRACKS = 'tracks';
    final public const RELATION_USER = 'user';

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Playlist::ATTRIBUTE_DESCRIPTION,
        Playlist::ATTRIBUTE_NAME,
        Playlist::ATTRIBUTE_USER,
        Playlist::ATTRIBUTE_VISIBILITY,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => PlaylistCreated::class,
        'deleted' => PlaylistDeleted::class,
        'updated' => PlaylistUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Playlist::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Playlist::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return HasHashids::ATTRIBUTE_HASHID;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::class,
        ];
    }

    /**
     * Get the numbers used to encode the model's hashids.
     *
     * @return array
     */
    public function hashids(): array
    {
        return array_filter([
            $this->user_id,
            $this->playlist_id,
        ]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->user === null ? $this->getName() : $this->user->getName();
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibility === PlaylistVisibility::PUBLIC;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Playlist::ATTRIBUTE_USER);
    }

    /**
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function first(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, Playlist::ATTRIBUTE_FIRST);
    }

    /**
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function last(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, Playlist::ATTRIBUTE_LAST);
    }

    /**
     * @return MorphToMany<Image, $this, Imageable, 'playlistimage'>
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGEABLE_ID, Imageable::ATTRIBUTE_IMAGE)
            ->using(Imageable::class)
            ->as('playlistimage')
            ->withTimestamps();
    }

    /**
     * @return HasMany<PlaylistTrack, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Only get the attributes as an array to prevent recursive toArray() calls.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return $this->attributesToArray();
    }
}
