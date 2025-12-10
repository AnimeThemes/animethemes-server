<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoCreated;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoDeleted;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\AnimeThemeEntryVideoFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property AnimeThemeEntry $animethemeentry
 * @property int $entry_id
 * @property Video $video
 * @property int $video_id
 *
 * @method static AnimeThemeEntryVideoFactory factory(...$parameters)
 */
class AnimeThemeEntryVideo extends BasePivot
{
    final public const string TABLE = 'anime_theme_entry_video';

    final public const string ATTRIBUTE_ENTRY = 'entry_id';
    final public const string ATTRIBUTE_VIDEO = 'video_id';

    final public const string RELATION_ANIME = 'animethemeentry.animetheme.anime';
    final public const string RELATION_ARTISTS = 'animethemeentry.animetheme.song.artists';
    final public const string RELATION_ENTRY = 'animethemeentry';
    final public const string RELATION_IMAGES = 'animethemeentry.animetheme.anime.images';
    final public const string RELATION_SONG = 'animethemeentry.animetheme.song';
    final public const string RELATION_VIDEO = 'video';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeThemeEntryVideo::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            AnimeThemeEntryVideo::ATTRIBUTE_ENTRY,
            AnimeThemeEntryVideo::ATTRIBUTE_VIDEO,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        AnimeThemeEntryVideo::ATTRIBUTE_ENTRY,
        AnimeThemeEntryVideo::ATTRIBUTE_VIDEO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => AnimeThemeEntryVideoCreated::class,
        'deleted' => AnimeThemeEntryVideoDeleted::class,
    ];

    /**
     * Gets the video that owns the video entry.
     *
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO);
    }

    /**
     * Gets the entry that owns the video entry.
     *
     * @return BelongsTo<AnimeThemeEntry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY);
    }
}
