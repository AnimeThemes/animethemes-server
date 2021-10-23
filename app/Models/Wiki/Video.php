<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\Wiki\Streamable;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoCreating;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\AnimeThemeEntryVideo;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Traits\CastsEnums;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Database\Factories\Wiki\VideoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Video.
 *
 * @property Collection $animethemeentries
 * @property string $basename
 * @property string $filename
 * @property bool $lyrics
 * @property string $mimetype
 * @property bool $nc
 * @property Enum $overlap
 * @property string $path
 * @property int|null $resolution
 * @property int $size
 * @property Enum|null $source
 * @property bool $subbed
 * @property string[] $tags
 * @property bool $uncen
 * @property int $video_id
 *
 * @method static VideoFactory factory(...$parameters)
 */
class Video extends BaseModel implements Streamable, Viewable
{
    use CastsEnums;
    use \ElasticScoutDriverPlus\Searchable;
    use InteractsWithViews;
    use Searchable;

    public const TABLE = 'videos';

    public const ATTRIBUTE_BASENAME = 'basename';
    public const ATTRIBUTE_FILENAME = 'filename';
    public const ATTRIBUTE_ID = 'video_id';
    public const ATTRIBUTE_LYRICS = 'lyrics';
    public const ATTRIBUTE_MIMETYPE = 'mimetype';
    public const ATTRIBUTE_NC = 'nc';
    public const ATTRIBUTE_OVERLAP = 'overlap';
    public const ATTRIBUTE_PATH = 'path';
    public const ATTRIBUTE_RESOLUTION = 'resolution';
    public const ATTRIBUTE_SIZE = 'size';
    public const ATTRIBUTE_SOURCE = 'source';
    public const ATTRIBUTE_SUBBED = 'subbed';
    public const ATTRIBUTE_TAGS = 'tags';
    public const ATTRIBUTE_UNCEN = 'uncen';

    public const RELATION_ANIME = 'animethemeentries.animetheme.anime';
    public const RELATION_ANIMESYNONYMS = 'animethemeentries.animetheme.anime.animesynonyms';
    public const RELATION_ANIMETHEME = 'animethemeentries.animetheme';
    public const RELATION_ANIMETHEMEENTRIES = 'animethemeentries';
    public const RELATION_SONG = 'animethemeentries.animetheme.song';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Video::ATTRIBUTE_BASENAME,
        Video::ATTRIBUTE_FILENAME,
        Video::ATTRIBUTE_MIMETYPE,
        Video::ATTRIBUTE_PATH,
        Video::ATTRIBUTE_SIZE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => VideoCreated::class,
        'creating' => VideoCreating::class,
        'deleted' => VideoDeleted::class,
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
     * @var array
     */
    protected $appends = [
        Video::ATTRIBUTE_TAGS,
    ];

    /**
     * The array of tags used to uniquely identify the video within the context of a theme.
     *
     * @return array
     */
    public function getTagsAttribute(): array
    {
        $tags = [];

        if ($this->nc) {
            $tags[] = 'NC';
        }
        if (! empty($this->source) && ($this->source->is(VideoSource::BD) || $this->source->is(VideoSource::DVD))) {
            $tags[] = $this->source->description;
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
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['entries'] = $this->animethemeentries->map(function (AnimeThemeEntry $entry) {
            return $entry->toSearchableArray();
        })->toArray();

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
        return Video::ATTRIBUTE_BASENAME;
    }

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        Video::ATTRIBUTE_OVERLAP => VideoOverlap::class,
        Video::ATTRIBUTE_SOURCE => VideoSource::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        Video::ATTRIBUTE_LYRICS => 'boolean',
        Video::ATTRIBUTE_NC => 'boolean',
        Video::ATTRIBUTE_OVERLAP => 'int',
        Video::ATTRIBUTE_SIZE => 'int',
        Video::ATTRIBUTE_SOURCE => 'int',
        Video::ATTRIBUTE_SUBBED => 'boolean',
        Video::ATTRIBUTE_UNCEN => 'boolean',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->basename;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get MIME type.
     *
     * @return string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get name of storage disk.
     *
     * @return string
     */
    public function getDisk(): string
    {
        return 'videos';
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
            ->withTimestamps();
    }
}
