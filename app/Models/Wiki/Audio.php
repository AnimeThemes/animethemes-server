<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\Streamable;
use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Models\BaseModel;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Database\Factories\Wiki\AudioFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Audio.
 *
 * @property int $audio_id
 * @property string $basename
 * @property string $filename
 * @property string $link
 * @property string $mimetype
 * @property string $path
 * @property int $size
 * @property Collection<int, Video> $videos
 *
 * @method static AudioFactory factory(...$parameters)
 */
class Audio extends BaseModel implements Streamable, Viewable
{
    use InteractsWithViews;

    final public const TABLE = 'audios';

    final public const ATTRIBUTE_BASENAME = 'basename';
    final public const ATTRIBUTE_FILENAME = 'filename';
    final public const ATTRIBUTE_ID = 'audio_id';
    final public const ATTRIBUTE_LINK = 'link';
    final public const ATTRIBUTE_MIMETYPE = 'mimetype';
    final public const ATTRIBUTE_PATH = 'path';
    final public const ATTRIBUTE_SIZE = 'size';

    final public const RELATION_VIDEOS = 'videos';
    final public const RELATION_VIEWS = 'views';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Audio::ATTRIBUTE_BASENAME,
        Audio::ATTRIBUTE_FILENAME,
        Audio::ATTRIBUTE_MIMETYPE,
        Audio::ATTRIBUTE_PATH,
        Audio::ATTRIBUTE_SIZE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AudioCreated::class,
        'deleted' => AudioDeleted::class,
        'restored' => AudioRestored::class,
        'updated' => AudioUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Audio::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Audio::ATTRIBUTE_ID;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Audio::ATTRIBUTE_LINK,
    ];

    /**
     * The link of the audio.
     *
     * @return string
     */
    public function getLinkAttribute(): string
    {
        return route('audio.show', $this);
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
        return Audio::ATTRIBUTE_BASENAME;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Audio::ATTRIBUTE_SIZE => 'int',
        ];
    }

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
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->path();
    }

    /**
     * Get the path of the streamable model in the filesystem.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the basename of the streamable model.
     *
     * @return string
     */
    public function basename(): string
    {
        return $this->basename;
    }

    /**
     * Get the MIME type / content type of the streamable model.
     *
     * @return string
     */
    public function mimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get the content length of the streamable model.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Get the videos that use this audio.
     *
     * @return HasMany<Video, $this>
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, Video::ATTRIBUTE_AUDIO);
    }
}
