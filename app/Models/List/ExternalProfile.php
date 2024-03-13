<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class ExternalProfile.
 *
 * @property int $profile_id
 * @property Collection<int, ExternalEntry> $externalentries
 * @property ExternalProfileSite $site
 * @property int|null $user_id
 * @property User|null $user
 * @property string $username
 * @property ExternalProfileVisibility $visibility
 */
class ExternalProfile extends BaseModel
{
    use Actionable;
    use Searchable;

    final public const TABLE = 'external_profiles';

    final public const ATTRIBUTE_ID = 'profile_id';
    final public const ATTRIBUTE_USERNAME = 'username';
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_VISIBILITY = 'visibility';
    final public const ATTRIBUTE_USER = 'user_id';

    final public const RELATION_ANIMES = 'externalentries.anime';
    final public const RELATION_EXTERNAL_ENTRIES = 'externalentries';
    final public const RELATION_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ExternalProfile::ATTRIBUTE_USERNAME,
        ExternalProfile::ATTRIBUTE_SITE,
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
    protected $table = ExternalProfile::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ExternalProfile::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        ExternalProfile::ATTRIBUTE_SITE => ExternalProfileSite::class,
        ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::class,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->username;
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
        return ExternalProfile::ATTRIBUTE_ID;
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        return ExternalProfileVisibility::PUBLIC === $this->visibility;
    }

    /**
     * Get the entries for the user profile.
     *
     * @return HasMany
     */
    public function externalentries(): HasMany
    {
        return $this->hasMany(ExternalEntry::class, ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE);
    }

    /**
     * Get the user that owns the user profile.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, ExternalProfile::ATTRIBUTE_USER);
    }

    /**
     * Only get the attributes as an array to prevent recursive toArray() calls.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->attributesToArray();
    }
}
