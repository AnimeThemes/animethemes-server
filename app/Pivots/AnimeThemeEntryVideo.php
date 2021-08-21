<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeThemeEntryVideo\AnimeThemeEntryAnimeThemeCreatedVideo;
use App\Events\Pivot\AnimeThemeEntryVideo\AnimeThemeEntryAnimeThemeDeletedVideo;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Database\Factories\Pivots\AnimeThemeEntryVideoFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeThemeEntryVideo.
 *
 * @property Video $video
 * @property AnimeThemeEntry $animethemeentry
 * @method static AnimeThemeEntryVideoFactory factory(...$parameters)
 */
class AnimeThemeEntryVideo extends BasePivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_theme_entry_video';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnimeThemeEntryAnimeThemeCreatedVideo::class,
        'deleted' => AnimeThemeEntryAnimeThemeDeletedVideo::class,
    ];

    /**
     * Gets the video that owns the video entry.
     *
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Video', 'video_id', 'video_id');
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return BelongsTo
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Anime\Theme\AnimeThemeEntry', 'entry_id', 'entry_id');
    }
}
