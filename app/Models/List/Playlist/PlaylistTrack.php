<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Schema;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Database\Factories\List\Playlist\PlaylistTrackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
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
    use HasFactory;
    use HasRecursiveRelationships;

    final public const string TABLE = 'playlist_tracks';

    final public const string ATTRIBUTE_ID = 'track_id';
    final public const string ATTRIBUTE_ENTRY = 'entry_id';
    final public const string ATTRIBUTE_NEXT = 'next_id';
    final public const string ATTRIBUTE_PLAYLIST = 'playlist_id';
    final public const string ATTRIBUTE_PREVIOUS = 'previous_id';
    final public const string ATTRIBUTE_VIDEO = 'video_id';

    final public const string RELATION_ARTISTS = 'animethemeentry.animetheme.song.artists';
    final public const string RELATION_AUDIO = 'video.audio';
    final public const string RELATION_ENTRY = 'animethemeentry';
    final public const string RELATION_IMAGES = 'animethemeentry.animetheme.anime.images';
    final public const string RELATION_NEXT = 'next';
    final public const string RELATION_PLAYLIST = 'playlist';
    final public const string RELATION_PREVIOUS = 'previous';
    final public const string RELATION_THEME_GROUP = 'animethemeentry.animetheme.group';
    final public const string RELATION_VIDEO = 'video';

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
        PlaylistTrack::ATTRIBUTE_ENTRY,
        PlaylistTrack::ATTRIBUTE_PLAYLIST,
        PlaylistTrack::ATTRIBUTE_VIDEO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TrackCreated::class,
        'deleted' => TrackDeleted::class,
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return HasHashids::ATTRIBUTE_HASHID;
    }

    /**
     * Get the numbers used to encode the model's hashids.
     */
    public function hashids(): array
    {
        return [
            $this->playlist_id,
            $this->track_id,
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        $subtitle = Str::of("($this->hashid) ");
        $user = $this->playlist->user;

        if ($user) {
            $subtitle = $subtitle
                ->append($user->getName())
                ->append(' - ');
        }

        return $subtitle->append($this->playlist->getName())->__toString();
    }

    /**
     * @return BelongsTo<AnimeThemeEntry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, PlaylistTrack::ATTRIBUTE_ENTRY);
    }

    /**
     * @return BelongsTo<Playlist, $this>
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    /**
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_PREVIOUS);
    }

    /**
     * @return BelongsTo<PlaylistTrack, $this>
     */
    public function next(): BelongsTo
    {
        return $this->belongsTo(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_NEXT);
    }

    /**
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, PlaylistTrack::ATTRIBUTE_VIDEO);
    }

    /**
     * Get the schema for the model.
     */
    public function schema(): TrackSchema
    {
        return new TrackSchema();
    }
}
