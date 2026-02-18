<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Contracts\Models\Streamable;
use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioForceDeleting;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Models\BaseModel;
use Database\Factories\Wiki\AudioFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
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
class Audio extends BaseModel implements Auditable, SoftDeletable, Streamable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'audios';

    final public const string ATTRIBUTE_BASENAME = 'basename';
    final public const string ATTRIBUTE_FILENAME = 'filename';
    final public const string ATTRIBUTE_ID = 'audio_id';
    final public const string ATTRIBUTE_LINK = 'link';
    final public const string ATTRIBUTE_MIMETYPE = 'mimetype';
    final public const string ATTRIBUTE_PATH = 'path';
    final public const string ATTRIBUTE_SIZE = 'size';

    final public const string RELATION_VIDEOS = 'videos';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => AudioCreated::class,
        'deleted' => AudioDeleted::class,
        'forceDeleting' => AudioForceDeleting::class,
        'restored' => AudioRestored::class,
        'updated' => AudioUpdated::class,
    ];

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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Audio::ATTRIBUTE_LINK,
    ];

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

    protected function link(): Attribute
    {
        return Attribute::make(function (): ?string {
            if ($this->hasAttribute($this->getRouteKeyName()) && $this->exists) {
                return route('audio.show', $this);
            }

            return null;
        });
    }

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Audio::ATTRIBUTE_BASENAME;
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
     * @return HasMany<Video, $this>
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, Video::ATTRIBUTE_AUDIO);
    }
}
