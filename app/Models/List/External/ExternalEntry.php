<?php

declare(strict_types=1);

namespace App\Models\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Database\Factories\List\External\ExternalEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $entry_id
 * @property int|null $anime_id
 * @property Anime|null $anime
 * @property int $profile_id
 * @property ExternalProfile $externalprofile
 * @property bool $is_favorite
 * @property float|null $score
 * @property ExternalEntryWatchStatus $watch_status
 *
 * @method static ExternalEntryFactory factory(...$parameters)
 */
class ExternalEntry extends BaseModel
{
    use HasFactory;

    final public const TABLE = 'external_entries';

    final public const ATTRIBUTE_ID = 'entry_id';
    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_PROFILE = 'profile_id';
    final public const ATTRIBUTE_IS_FAVORITE = 'is_favorite';
    final public const ATTRIBUTE_SCORE = 'score';
    final public const ATTRIBUTE_WATCH_STATUS = 'watch_status';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_PROFILE = 'externalprofile';
    final public const RELATION_USER = 'externalprofile.user';

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ExternalEntry::ATTRIBUTE_ANIME,
        ExternalEntry::ATTRIBUTE_PROFILE,
        ExternalEntry::ATTRIBUTE_IS_FAVORITE,
        ExternalEntry::ATTRIBUTE_SCORE,
        ExternalEntry::ATTRIBUTE_WATCH_STATUS,
    ];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ExternalEntry::ATTRIBUTE_IS_FAVORITE => 'bool',
            ExternalEntry::ATTRIBUTE_WATCH_STATUS => ExternalEntryWatchStatus::class,
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return $this->anime->getName();
    }

    /**
     * Get the fields to perform an update.
     *
     * @return string[]
     */
    public static function fieldsForUpdate(): array
    {
        return [
            ExternalEntry::ATTRIBUTE_IS_FAVORITE,
            ExternalEntry::ATTRIBUTE_SCORE,
            ExternalEntry::ATTRIBUTE_WATCH_STATUS,
        ];
    }

    /**
     * Get the anime that owns the user external entry.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, ExternalEntry::ATTRIBUTE_ANIME);
    }

    /**
     * Get the user profile that owns the user profile.
     *
     * @return BelongsTo<ExternalProfile, $this>
     */
    public function externalprofile(): BelongsTo
    {
        return $this->belongsTo(ExternalProfile::class, ExternalEntry::ATTRIBUTE_PROFILE);
    }
}
