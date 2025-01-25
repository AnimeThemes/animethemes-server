<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackRestored;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Schema;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Database\Factories\List\Playlist\PlaylistTrackFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * Class PlaylistTrack.
 *
 * @property int $entry_id
 * @property AnimeThemeEntry $animethemeentry
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
class PlaylistTrack extends BaseModel implements HasHashids, InteractsWithSchema
{
    use HasRecursiveRelationships;

    final public const TABLE = 'playlist_tracks';

    final public const ATTRIBUTE_ID = 'track_id';
    final public const ATTRIBUTE_ENTRY = 'entry_id';
    final public const ATTRIBUTE_NEXT = 'next_id';
    final public const ATTRIBUTE_PLAYLIST = 'playlist_id';
    final public const ATTRIBUTE_PREVIOUS = 'previous_id';
    final public const ATTRIBUTE_VIDEO = 'video_id';

    final public const RELATION_ARTISTS = 'animethemeentry.animetheme.song.artists';
    final public const RELATION_AUDIO = 'video.audio';
    final public const RELATION_ENTRY = 'animethemeentry';
    final public const RELATION_IMAGES = 'animethemeentry.animetheme.anime.images';
    final public const RELATION_NEXT = 'next';
    final public const RELATION_PLAYLIST = 'playlist';
    final public const RELATION_PREVIOUS = 'previous';
    final public const RELATION_THEME_GROUP = 'animethemeentry.animetheme.group';
    final public const RELATION_VIDEO = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        PlaylistTrack::ATTRIBUTE_ENTRY,
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
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        $subtitle = Str::of("($this->hashid) ");
        $user = $this->playlist->user;

        if ($user) {
            $subtitle = $subtitle
                ->append($user->getName())
                ->append(' - ');
        }

        $subtitle = $subtitle->append($this->playlist->getName());

        return $subtitle->__toString();
    }

    /**
     * Get the entry of the track.
     *
     * @return BelongsTo<AnimeThemeEntry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, PlaylistTrack::ATTRIBUTE_ENTRY);
    }

    /**
     * Get the playlist the track belongs to.
     *
     * @return BelongsTo<Playlist, $this>
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Get the previous track.
     *
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_PREVIOUS);
    }

    /**
     * Get the next track.
     *
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function next(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_NEXT);
    }

    /**
     * Get the video of the track.
     *
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, PlaylistTrack::ATTRIBUTE_VIDEO);
    }

    /**
     * Get the schema for the model.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new TrackSchema();
    }
}
