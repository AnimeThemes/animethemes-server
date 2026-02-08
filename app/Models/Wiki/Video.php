<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\Aggregate\AggregatesView;
use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasAggregateViews;
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
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Database\Factories\Wiki\VideoFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

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
 * @property int $priority
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
class Video extends BaseModel implements Auditable, HasAggregateViews, SoftDeletable, Streamable
{
    use AggregatesView;
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'videos';

    final public const string ATTRIBUTE_AUDIO = 'audio_id';
    final public const string ATTRIBUTE_BASENAME = 'basename';
    final public const string ATTRIBUTE_FILENAME = 'filename';
    final public const string ATTRIBUTE_ID = 'video_id';
    final public const string ATTRIBUTE_LINK = 'link';
    final public const string ATTRIBUTE_LYRICS = 'lyrics';
    final public const string ATTRIBUTE_MIMETYPE = 'mimetype';
    final public const string ATTRIBUTE_NC = 'nc';
    final public const string ATTRIBUTE_OVERLAP = 'overlap';
    final public const string ATTRIBUTE_PATH = 'path';
    final public const string ATTRIBUTE_PRIORITY = 'priority';
    final public const string ATTRIBUTE_RESOLUTION = 'resolution';
    final public const string ATTRIBUTE_SIZE = 'size';
    final public const string ATTRIBUTE_SOURCE = 'source';
    final public const string ATTRIBUTE_SUBBED = 'subbed';
    final public const string ATTRIBUTE_TAGS = 'tags';
    final public const string ATTRIBUTE_UNCEN = 'uncen';

    final public const string RELATION_ANIME = 'animethemeentries.animetheme.anime';
    final public const string RELATION_ANIMESYNONYMS = 'animethemeentries.animetheme.anime.animesynonyms';
    final public const string RELATION_ANIMETHEME = 'animethemeentries.animetheme';
    final public const string RELATION_ANIMETHEMEENTRIES = 'animethemeentries';
    final public const string RELATION_GROUP = 'animethemeentries.animetheme.group';
    final public const string RELATION_AUDIO = 'audio';
    final public const string RELATION_SCRIPT = 'videoscript';
    final public const string RELATION_SONG = 'animethemeentries.animetheme.song';
    final public const string RELATION_TRACKS = 'tracks';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => VideoCreated::class,
        'deleted' => VideoDeleted::class,
        'forceDeleting' => VideoForceDeleting::class,
        'restored' => VideoRestored::class,
        'updated' => VideoUpdated::class,
    ];

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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Video::ATTRIBUTE_LINK,
        Video::ATTRIBUTE_PRIORITY,
        Video::ATTRIBUTE_TAGS,
    ];

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

    protected function link(): Attribute
    {
        return Attribute::make(function (): ?string {
            if ($this->hasAttribute($this->getRouteKeyName()) && $this->exists) {
                return route('video.show', $this);
            }

            return null;
        });
    }

    /**
     * Get the priority score for the video.
     * Higher scores increase the likelihood of the video to be the source of an audio track.
     */
    protected function priority(): Attribute
    {
        return Attribute::make(function (): int {
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
        });
    }

    /**
     * The array of tags used to uniquely identify the video within the context of a theme.
     */
    protected function tags(): Attribute
    {
        return Attribute::make(function (): string {
            $tags = [];

            if ($this->nc) {
                $tags[] = 'NC';
            }
            if ($this->source === VideoSource::BD || $this->source === VideoSource::DVD) {
                $tags[] = $this->source->localize();
            }
            if (filled($this->resolution) && $this->resolution !== 720) {
                $tags[] = strval($this->resolution);
            }

            if ($this->subbed) {
                $tags[] = 'Subbed';
            } elseif ($this->lyrics) {
                $tags[] = 'Lyrics';
            }

            return implode('', $tags);
        });
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
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
            fn (AnimeThemeEntry $entry): array => $entry->toSearchableArray()
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

    public function getName(): string
    {
        return $this->basename;
    }

    public function getSubtitle(): string
    {
        return $this->path();
    }

    public function path(): string
    {
        return $this->path;
    }

    public function basename(): string
    {
        return $this->basename;
    }

    public function mimetype(): string
    {
        return $this->mimetype;
    }

    public function size(): int
    {
        return $this->size;
    }

    /**
     * @return BelongsToMany<AnimeThemeEntry, $this, AnimeThemeEntryVideo>
     */
    public function animethemeentries(): BelongsToMany
    {
        return $this->belongsToMany(AnimeThemeEntry::class, AnimeThemeEntryVideo::TABLE, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
            ->using(AnimeThemeEntryVideo::class)
            ->as(AnimeThemeEntryVideoJsonResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<Audio, $this>
     */
    public function audio(): BelongsTo
    {
        return $this->belongsTo(Audio::class, Video::ATTRIBUTE_AUDIO);
    }

    /**
     * @return HasOne<VideoScript, $this>
     */
    public function videoscript(): HasOne
    {
        return $this->hasOne(VideoScript::class, VideoScript::ATTRIBUTE_VIDEO);
    }

    /**
     * @return HasMany<PlaylistTrack, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_VIDEO)
            ->whereRelation(PlaylistTrack::RELATION_PLAYLIST, Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);
    }
}
