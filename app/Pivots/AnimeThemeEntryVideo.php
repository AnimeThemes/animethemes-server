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
 * @property AnimeThemeEntry $animethemeentry
 * @property Video $video
 *
 * @method static AnimeThemeEntryVideoFactory factory(...$parameters)
 */
class AnimeThemeEntryVideo extends BasePivot
{
    public const TABLE = 'anime_theme_entry_video';

    public const ATTRIBUTE_ENTRY = 'entry_id';
    public const ATTRIBUTE_VIDEO = 'video_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeThemeEntryVideo::TABLE;

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
        return $this->belongsTo(Video::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO);
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return BelongsTo
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY);
    }
}
