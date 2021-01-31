<?php

namespace App\Pivots;

use App\Events\Pivot\VideoEntry\VideoEntryCreated;
use App\Events\Pivot\VideoEntry\VideoEntryDeleted;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VideoEntry extends Pivot
{
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
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => VideoEntryCreated::class,
        'deleted' => VideoEntryDeleted::class,
    ];

    /**
     * Gets the video that owns the video entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video()
    {
        return $this->belongsTo('App\Models\Video', 'video_id', 'video_id');
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo('App\Models\Entry', 'entry_id', 'entry_id');
    }
}
