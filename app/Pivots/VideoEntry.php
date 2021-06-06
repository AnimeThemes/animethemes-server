<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\VideoEntry\VideoEntryCreated;
use App\Events\Pivot\VideoEntry\VideoEntryDeleted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class VideoEntry.
 */
class VideoEntry extends BasePivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry_video';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => VideoEntryCreated::class,
        'deleted' => VideoEntryDeleted::class,
    ];

    /**
     * Gets the video that owns the video entry.
     *
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo('App\Models\Video', 'video_id', 'video_id');
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return BelongsTo
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo('App\Models\Entry', 'entry_id', 'entry_id');
    }
}
