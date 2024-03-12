<?php

declare(strict_types=1);

namespace App\Models\List\External;

use App\Enums\Models\List\AnimeWatchStatus;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Nova\Actions\Actionable;

/**
 * Class ExternalEntry.
 *
 * @property int $entry_id
 * @property int $anime_id
 * @property Anime $anime
 * @property int $external_profile_id
 * @property ExternalProfile $external_profile
 * @property bool|null $isFavourite
 * @property float|null $score
 * @property AnimeWatchStatus|null $watch_status
 */
class ExternalEntry extends BaseModel
{
    use Actionable;
    use Searchable;

    final public const TABLE = 'external_entries';

    final public const ATTRIBUTE_ID = 'entry_id';
    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_EXTERNAL_PROFILE = 'external_profile_id';
    final public const ATTRIBUTE_IS_FAVOURITE = 'isFavourite';
    final public const ATTRIBUTE_SCORE = 'score';
    final public const ATTRIBUTE_WATCH_STATUS = 'watch_status';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_EXTERNAL_PROFILE = 'external_profile';
    final public const RELATION_USER = 'external_profile.user';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ExternalEntry::ATTRIBUTE_ANIME,
        ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE,
        ExternalEntry::ATTRIBUTE_IS_FAVOURITE,
        ExternalEntry::ATTRIBUTE_SCORE,
        ExternalEntry::ATTRIBUTE_WATCH_STATUS,
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
    protected $table = ExternalEntry::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ExternalEntry::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        ExternalEntry::ATTRIBUTE_WATCH_STATUS => AnimeWatchStatus::class,
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
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return ExternalEntry::ATTRIBUTE_ID;
    }

    /**
     * Get the anime that owns the user external entry.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, ExternalEntry::ATTRIBUTE_ANIME);
    }

    /**
     * Get the user profile that owns the user profile.
     *
     * @return BelongsTo
     */
    public function external_profile(): BelongsTo
    {
        return $this->belongsTo(ExternalProfile::class, ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE);
    }
}