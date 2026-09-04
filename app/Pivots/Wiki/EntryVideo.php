<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\EntryVideo\EntryVideoCreated;
use App\Events\Pivot\Wiki\EntryVideo\EntryVideoDeleted;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\EntryVideoFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property Entry $animethemeentry
 * @property int $entry_id
 * @property Video $video
 * @property int $video_id
 *
 * @method static EntryVideoFactory factory(...$parameters)
 */
#[Table(EntryVideo::TABLE)]
class EntryVideo extends BasePivot implements Auditable
{
    use HasAudits;

    final public const string TABLE = 'entry_video';

    final public const string ATTRIBUTE_ENTRY = 'entry_id';

    final public const string ATTRIBUTE_VIDEO = 'video_id';

    final public const string RELATION_ANIME = 'animethemeentry.animetheme.anime';

    final public const string RELATION_ARTISTS = 'animethemeentry.animetheme.song.artists';

    final public const string RELATION_ENTRY = 'animethemeentry';

    final public const string RELATION_IMAGES = 'animethemeentry.animetheme.anime.images';

    final public const string RELATION_SONG = 'animethemeentry.animetheme.song';

    final public const string RELATION_VIDEO = 'video';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => EntryVideoCreated::class,
        'deleted' => EntryVideoDeleted::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        EntryVideo::ATTRIBUTE_ENTRY,
        EntryVideo::ATTRIBUTE_VIDEO,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            EntryVideo::ATTRIBUTE_ENTRY => 'int',
            EntryVideo::ATTRIBUTE_VIDEO => 'int',
        ];
    }

    /**
     * Gets the video that owns the video entry.
     *
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, EntryVideo::ATTRIBUTE_VIDEO);
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return BelongsTo<Entry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(Entry::class, EntryVideo::ATTRIBUTE_ENTRY);
    }
}
