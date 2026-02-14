<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Database\Factories\User\WatchHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property AnimeThemeEntry $animethemeentry
 * @property int $entry_id
 * @property User $user
 * @property int $user_id
 * @property Video $video
 * @property int $video_id
 *
 * @method static WatchHistoryFactory factory(...$parameters)
 */
class WatchHistory extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'watch_history';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_ENTRY = 'entry_id';
    final public const string ATTRIBUTE_USER = 'user_id';
    final public const string ATTRIBUTE_VIDEO = 'video_id';

    final public const string RELATION_ENTRY = 'animethemeentry';
    final public const string RELATION_USER = 'user';
    final public const string RELATION_VIDEO = 'video';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = WatchHistory::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = WatchHistory::ATTRIBUTE_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        WatchHistory::ATTRIBUTE_ENTRY,
        WatchHistory::ATTRIBUTE_USER,
        WatchHistory::ATTRIBUTE_VIDEO,
    ];

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return '';
    }

    /**
     * @return BelongsTo<AnimeThemeEntry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, WatchHistory::ATTRIBUTE_ENTRY);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, WatchHistory::ATTRIBUTE_USER);
    }

    /**
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, WatchHistory::ATTRIBUTE_VIDEO);
    }
}
