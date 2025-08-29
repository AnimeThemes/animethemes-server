<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Aggregate\AggregatesLike;
use App\Concerns\Models\Aggregate\AggregatesView;
use App\Concerns\Models\InteractsWithLikes;
use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\HasAggregateLikes;
use App\Contracts\Models\HasAggregateViews;
use App\Contracts\Models\Likeable;
use App\Contracts\Models\SoftDeletable;
use App\Contracts\Models\Streamable;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoForceDeleting;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\User\Encode;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Database\Factories\Wiki\VideoFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, AnimeThemeEntry> $animethemeentries
 * @property Audio|null $audio
 * @property int|null $audio_id
 * @property string $basename
 * @property string $filename
 * @property string $link
 * @property bool $lyrics
 * @property string $mimetype
 * @property bool $nc
 * @property VideoOverlap $overlap
 * @property string $path
 * @property Collection<int, PlaylistTrack> $tracks
 * @property int|null $resolution
 * @property VideoScript|null $videoscript
 * @property int $size
 * @property VideoSource|null $source
 * @property bool $subbed
 * @property array<int, string> $tags
 * @property bool $uncen
 * @property int $video_id
 *
 * @method static VideoFactory factory(...$parameters)
 */
class Video extends BaseModel implements HasAggregateLikes, HasAggregateViews, Likeable, SoftDeletable, Streamable, Viewable
{
    use AggregatesLike;
    use AggregatesView;
    use HasFactory;
    use InteractsWithLikes;
    use InteractsWithViews;
    use Reportable;
    use Searchable;
    use SoftDeletes;

    final public const TABLE = 'videos';

    final public const ATTRIBUTE_AUDIO = 'audio_id';
    final public const ATTRIBUTE_BASENAME = 'basename';
    final public const ATTRIBUTE_FILENAME = 'filename';
    final public const ATTRIBUTE_ID = 'video_id';
    final public const ATTRIBUTE_LINK = 'link';
    final public const ATTRIBUTE_LYRICS = 'lyrics';
    final public const ATTRIBUTE_MIMETYPE = 'mimetype';
    final public const ATTRIBUTE_NC = 'nc';
    final public const ATTRIBUTE_OVERLAP = 'overlap';
    final public const ATTRIBUTE_PATH = 'path';
    final public const ATTRIBUTE_RESOLUTION = 'resolution';
    final public const ATTRIBUTE_SIZE = 'size';
    final public const ATTRIBUTE_SOURCE = 'source';
    final public const ATTRIBUTE_SUBBED = 'subbed';
    final public const ATTRIBUTE_TAGS = 'tags';
    final public const ATTRIBUTE_UNCEN = 'uncen';

    final public const RELATION_ANIME = 'animethemeentries.animetheme.anime';
    final public const RELATION_ANIMESYNONYMS = 'animethemeentries.animetheme.anime.animesynonyms';
    final public const RELATION_ANIMETHEME = 'animethemeentries.animetheme';
    final public const RELATION_ANIMETHEMEENTRIES = 'animethemeentries';
    final public const RELATION_GROUP = 'animethemeentries.animetheme.group';
    final public const RELATION_AUDIO = 'audio';
    final public const RELATION_SCRIPT = 'videoscript';
    final public const RELATION_SONG = 'animethemeentries.animetheme.song';
    final public const RELATION_TRACKS = 'tracks';
    final public const RELATION_VIEWS = 'views';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Video::ATTRIBUTE_AUDIO,
        Video::ATTRIBUTE_BASENAME,
        Video::ATTRIBUTE_FILENAME,
        Video::ATTRIBUTE_LYRICS,
        Video::ATTRIBUTE_MIMETYPE,
        Video::ATTRIBUTE_NC,
        Video::ATTRIBUTE_OVERLAP,
        Video::ATTRIBUTE_PATH,
        Video::ATTRIBUTE_RESOLUTION,
        Video::ATTRIBUTE_SIZE,
        Video::ATTRIBUTE_SOURCE,
        Video::ATTRIBUTE_SUBBED,
        Video::ATTRIBUTE_UNCEN,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => VideoCreated::class,
        'deleted' => VideoDeleted::class,
        'forceDeleting' => VideoForceDeleting::class,
        'restored' => VideoRestored::class,
        'updated' => VideoUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Video::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Video::ATTRIBUTE_ID;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Video::ATTRIBUTE_LINK,
        Video::ATTRIBUTE_TAGS,
    ];

    /**
     * The link of the video.
     */
    public function getLinkAttribute(): ?string
    {
        if ($this->hasAttribute($this->getRouteKeyName()) && $this->exists) {
            return route('video.show', $this);
        }

        return null;
    }

    /**
     * The array of tags used to uniquely identify the video within the context of a theme.
     *
     * @return string[]
     */
    public function getTagsAttribute(): array
    {
        $tags = [];

        if ($this->nc) {
            $tags[] = 'NC';
        }
        if ($this->source === VideoSource::BD || $this->source === VideoSource::DVD) {
            $tags[] = $this->source->localize();
        }
        if (! empty($this->resolution) && $this->resolution !== 720) {
            $tags[] = strval($this->resolution);
        }

        if ($this->subbed) {
            $tags[] = 'Subbed';
        } elseif ($this->lyrics) {
            $tags[] = 'Lyrics';
        }

        return $tags;
    }

    /**
     * Get the priority score for the video.
     * Higher scores increase the likelihood of the video to be the source of an audio track.
     */
    public function getSourcePriority(): int
    {
        $priority = intval($this->source?->getPriority());

        // Videos that play over the episode will likely have compressed audio
        if ($this->overlap === VideoOverlap::OVER) {
            $priority -= 8;
        }

        // Videos that transition to or from the episode may have compressed audio
        if ($this->overlap === VideoOverlap::TRANS) {
            $priority -= 5;
        }

        // De-prioritize hardsubbed videos
        if ($this->lyrics || $this->subbed) {
            $priority--;
        }

        return $priority;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            Video::RELATION_SONG,
            Video::RELATION_ANIMESYNONYMS,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $array['entries'] = $this->animethemeentries->map(
            fn (AnimeThemeEntry $entry) => $entry->toSearchableArray()
        )->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Video::ATTRIBUTE_BASENAME;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Video::ATTRIBUTE_LYRICS => 'boolean',
            Video::ATTRIBUTE_NC => 'boolean',
            Video::ATTRIBUTE_OVERLAP => VideoOverlap::class,
            Video::ATTRIBUTE_SIZE => 'int',
            Video::ATTRIBUTE_SOURCE => VideoSource::class,
            Video::ATTRIBUTE_SUBBED => 'boolean',
            Video::ATTRIBUTE_UNCEN => 'boolean',
        ];
    }

    public function getName(): string
    {
        return $this->basename;
    }

    public function getSubtitle(): string
    {
        return $this->path();
    }

    /**
     * Get the path of the streamable model in the filesystem.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the basename of the streamable model.
     */
    public function basename(): string
    {
        return $this->basename;
    }

    /**
     * Get the MIME type / content type of the streamable model.
     */
    public function mimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get the content length of the streamable model.
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Get the related entries.
     *
     * @return BelongsToMany
     */
    public function animethemeentries(): BelongsToMany
    {
        return $this->belongsToMany(AnimeThemeEntry::class, AnimeThemeEntryVideo::TABLE, Video::ATTRIBUTE_ID, AnimeThemeEntry::ATTRIBUTE_ID)
            ->using(AnimeThemeEntryVideo::class)
            ->as(AnimeThemeEntryVideoResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Gets the audio that the video uses.
     *
     * @return BelongsTo<Audio, $this>
     */
    public function audio(): BelongsTo
    {
        return $this->belongsTo(Audio::class, Video::ATTRIBUTE_AUDIO);
    }

    /**
     * Get the script that the video owns.
     *
     * @return HasOne<VideoScript, $this>
     */
    public function videoscript(): HasOne
    {
        return $this->hasOne(VideoScript::class, VideoScript::ATTRIBUTE_VIDEO);
    }

    /**
     * Get the tracks that use this video.
     *
     * @return HasMany<PlaylistTrack, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_VIDEO)
            ->whereRelation(PlaylistTrack::RELATION_PLAYLIST, Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);
    }

    /**
     * Get the encodes of the video.
     *
     * @return HasMany<Encode, $this>
     */
    public function encodes(): HasMany
    {
        return $this->hasMany(Encode::class, Encode::ATTRIBUTE_VIDEO);
    }
}
