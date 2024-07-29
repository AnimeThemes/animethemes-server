<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Contracts\Models\HasHashids;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\PlaylistDeleted;
use App\Events\List\Playlist\PlaylistRestored;
use App\Events\List\Playlist\PlaylistUpdated;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Database\Factories\List\PlaylistFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Playlist.
 *
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
class Playlist extends BaseModel implements HasHashids, Viewable
{
    use Searchable;
    use InteractsWithViews;

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
    final public const RELATION_VIEWS = 'views';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => PlaylistCreated::class,
        'deleted' => PlaylistDeleted::class,
        'restored' => PlaylistRestored::class,
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
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return HasHashids::ATTRIBUTE_HASHID;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::class,
    ];

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
        return $this->user === null ? $this->getName() : $this->user->getName();
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        return PlaylistVisibility::PUBLIC === $this->visibility;
    }

    /**
     * Get the user that owns the playlist.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Playlist::ATTRIBUTE_USER);
    }

    /**
     * Get the first track of the playlist.
     *
     * @return BelongsTo
     */
    public function first(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, Playlist::ATTRIBUTE_FIRST);
    }

    /**
     * Get the last track of the playlist.
     *
     * @return BelongsTo
     */
    public function last(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, Playlist::ATTRIBUTE_LAST);
    }

    /**
     * Get the images for the playlist.
     *
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, PlaylistImage::class, Playlist::ATTRIBUTE_ID, Image::ATTRIBUTE_ID)
            ->using(PlaylistImage::class)
            ->as(PlaylistImageResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the tracks that comprise the playlist.
     *
     * @return HasMany
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Only get the attributes as an array to prevent recursive toArray() calls.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->attributesToArray();
    }
}
