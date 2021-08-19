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
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Video.
 *
 * @property int $video_id
 * @property string $basename
 * @property string $filename
 * @property string $path
 * @property int $size
 * @property string $mimetype
 * @property int|null $resolution
 * @property bool $nc
 * @property bool $subbed
 * @property bool $lyrics
 * @property bool $uncen
 * @property Enum $overlap
 * @property Enum|null $source
 * @property string[] $tags
 * @property Collection $animethemeentries
 * @method static VideoFactory factory(...$parameters)
 */
class Video extends BaseModel implements Streamable, Viewable
{
    use CastsEnums;
    use InteractsWithViews;
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['basename', 'filename', 'path', 'size', 'mimetype'];

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
    protected $table = 'videos';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'video_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['tags'];

    /**
     * The array of tags used to uniquely identify the video within the context of a theme.
     *
     * @return array
     */
    public function getTagsAttribute(): array
    {
        $tags = [];

        if ($this->nc) {
            array_push($tags, 'NC');
        }
        if (! empty($this->source) && ($this->source->is(VideoSource::BD) || $this->source->is(VideoSource::DVD))) {
            array_push($tags, $this->source->description);
        }
        if (! empty($this->resolution) && $this->resolution !== 720) {
            array_push($tags, strval($this->resolution));
        }

        if ($this->subbed) {
            array_push($tags, 'Subbed');
        } elseif ($this->lyrics) {
            array_push($tags, 'Lyrics');
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
        return $query->with(['animethemeentries.animetheme.anime.animesynonyms', 'animethemeentries.animetheme.song']);
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
        return 'basename';
    }

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        'overlap' => VideoOverlap::class,
        'source' => VideoSource::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'overlap' => 'int',
        'source' => 'int',
        'size' => 'int',
        'nc' => 'boolean',
        'subbed' => 'boolean',
        'lyrics' => 'boolean',
        'uncen' => 'boolean',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->filename;
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
        return $this->belongsToMany('App\Models\Wiki\Anime\Theme\AnimeThemeEntry', 'anime_theme_entry_video', 'video_id', 'entry_id')
            ->using(AnimeThemeEntryVideo::class)
            ->withTimestamps();
    }
}
