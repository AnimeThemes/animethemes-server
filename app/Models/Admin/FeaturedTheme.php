<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Events\Admin\FeaturedTheme\FeaturedThemeCreated;
use App\Events\Admin\FeaturedTheme\FeaturedThemeDeleted;
use App\Events\Admin\FeaturedTheme\FeaturedThemeUpdated;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Database\Factories\Admin\FeaturedThemeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property Carbon|null $end_at
 * @property AnimeThemeEntry|null $animethemeentry
 * @property int $entry_id
 * @property int $feature_id
 * @property Carbon|null $start_at
 * @property User|null $user
 * @property int|null $user_id
 * @property Video|null $video
 * @property int|null $video_id
 *
 * @method static FeaturedThemeFactory factory(...$parameters)
 */
class FeaturedTheme extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'featured_themes';

    final public const string ATTRIBUTE_END_AT = 'end_at';
    final public const string ATTRIBUTE_ENTRY = 'entry_id';
    final public const string ATTRIBUTE_ID = 'featured_theme_id';
    final public const string ATTRIBUTE_START_AT = 'start_at';
    final public const string ATTRIBUTE_USER = 'user_id';
    final public const string ATTRIBUTE_VIDEO = 'video_id';

    final public const string RELATION_ANIME = 'animethemeentry.animetheme.anime';
    final public const string RELATION_ARTISTS = 'animethemeentry.animetheme.song.artists';
    final public const string RELATION_ENTRY = 'animethemeentry';
    final public const string RELATION_GROUP = 'animethemeentry.animetheme.group';
    final public const string RELATION_IMAGES = 'animethemeentry.animetheme.anime.images';
    final public const string RELATION_SONG = 'animethemeentry.animetheme.song';
    final public const string RELATION_THEME = 'animethemeentry.animetheme';
    final public const string RELATION_USER = 'user';
    final public const string RELATION_VIDEO = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        FeaturedTheme::ATTRIBUTE_END_AT,
        FeaturedTheme::ATTRIBUTE_ENTRY,
        FeaturedTheme::ATTRIBUTE_START_AT,
        FeaturedTheme::ATTRIBUTE_USER,
        FeaturedTheme::ATTRIBUTE_VIDEO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => FeaturedThemeCreated::class,
        'deleted' => FeaturedThemeDeleted::class,
        'updated' => FeaturedThemeUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = FeaturedTheme::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = FeaturedTheme::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            FeaturedTheme::ATTRIBUTE_END_AT => 'datetime',
            FeaturedTheme::ATTRIBUTE_START_AT => 'datetime',
        ];
    }

    public function getName(): string
    {
        return Str::of($this->start_at->format(AllowedDateFormat::YMD->value))
            ->append(' - ')
            ->append($this->end_at->format(AllowedDateFormat::YMD->value))
            ->__toString();
    }

    public function getSubtitle(): string
    {
        return $this->animethemeentry === null ? $this->getName() : $this->animethemeentry->getName();
    }

    /**
     * Get the user that recommended the featured theme.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, FeaturedTheme::ATTRIBUTE_USER);
    }

    /**
     * @return BelongsTo<AnimeThemeEntry, $this>
     */
    public function animethemeentry(): BelongsTo
    {
        return $this->belongsTo(AnimeThemeEntry::class, FeaturedTheme::ATTRIBUTE_ENTRY);
    }

    /**
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, FeaturedTheme::ATTRIBUTE_VIDEO);
    }
}
