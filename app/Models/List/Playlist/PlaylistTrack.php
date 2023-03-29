<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackRestored;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\Wiki\Video;
use Database\Factories\List\Playlist\PlaylistTrackFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Nova\Actions\Actionable;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * Class PlaylistTrack.
 *
 * @property PlaylistTrack|null $next
 * @property int $next_id
 * @property int $playlist_id
 * @property Playlist $playlist
 * @property PlaylistTrack|null $previous
 * @property int $previous_id
 * @property int $track_id
 * @property Video $video
 * @property int $video_id
 *
 * @method static PlaylistTrackFactory factory(...$parameters)
 */
class PlaylistTrack extends BaseModel implements HasHashids
{
    use Actionable;
    use HasRecursiveRelationships;

    final public const TABLE = 'playlist_tracks';

    final public const ATTRIBUTE_ID = 'track_id';
    final public const ATTRIBUTE_NEXT = 'next_id';
    final public const ATTRIBUTE_PLAYLIST = 'playlist_id';
    final public const ATTRIBUTE_PREVIOUS = 'previous_id';
    final public const ATTRIBUTE_VIDEO = 'video_id';

    final public const RELATION_ARTISTS = 'video.animethemeentries.animetheme.song.artists';
    final public const RELATION_AUDIO = 'video.audio';
    final public const RELATION_IMAGES = 'video.animethemeentries.animetheme.anime.images';
    final public const RELATION_NEXT = 'next';
    final public const RELATION_PLAYLIST = 'playlist';
    final public const RELATION_PREVIOUS = 'previous';
    final public const RELATION_VIDEO = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        PlaylistTrack::ATTRIBUTE_PLAYLIST,
        PlaylistTrack::ATTRIBUTE_VIDEO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TrackCreated::class,
        'deleted' => TrackDeleted::class,
        'restored' => TrackRestored::class,
        'updated' => TrackUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PlaylistTrack::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = PlaylistTrack::ATTRIBUTE_ID;

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
     * Get the numbers used to encode the model's hashids.
     *
     * @return array
     */
    public function hashids(): array
    {
        return [
            $this->playlist_id,
            $this->track_id,
        ];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get the playlist the track belongs to.
     *
     * @return BelongsTo
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Get the previous track.
     *
     * @return BelongsTo
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_PREVIOUS);
    }

    /**
     * Get the next track.
     *
     * @return BelongsTo
     */
    public function next(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_NEXT);
    }

    /**
     * Get the video of the track.
     *
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, PlaylistTrack::ATTRIBUTE_VIDEO);
    }
}
