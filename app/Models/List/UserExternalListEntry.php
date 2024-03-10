<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Enums\Models\List\AnimeWatchStatus;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Nova\Actions\Actionable;

/**
 * Class UserExternalListEntry.
 *
 * @property int $entry_id
 * @property int $anime_id
 * @property float|null $score
 * @property AnimeWatchStatus|null $watch_status
 * @property int $user_profile_id
 * @property Anime $anime
 * @property UserExternalProfile $profile
 */
class UserExternalListEntry extends BaseModel
{
    use Actionable;

    final public const TABLE = 'user_external_list_entries';

    final public const ATTRIBUTE_ID = 'entry_id';
    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_SCORE = 'score';
    final public const ATTRIBUTE_WATCH_STATUS = 'watch_status';
    final public const ATTRIBUTE_USER_PROFILE = 'user_profile_id';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_USER_PROFILE = 'user_profile';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        UserExternalListEntry::ATTRIBUTE_ANIME,
        UserExternalListEntry::ATTRIBUTE_SCORE,
        UserExternalListEntry::ATTRIBUTE_WATCH_STATUS,
        UserExternalListEntry::ATTRIBUTE_USER_PROFILE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = UserExternalListEntry::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = UserExternalListEntry::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        UserExternalListEntry::ATTRIBUTE_WATCH_STATUS => AnimeWatchStatus::class,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get the anime that owns the user list entry.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, UserExternalListEntry::ATTRIBUTE_ANIME);
    }

    /**
     * Get the user profile that owns the user profile.
     *
     * @return BelongsTo
     */
    public function user_profile(): BelongsTo
    {
        return $this->belongsTo(UserExternalProfile::class, UserExternalListEntry::ATTRIBUTE_USER_PROFILE);
    }
}
