<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileRestored;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Jobs\List\SyncExternalProfileJob;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use App\Models\List\External\ExternalToken;
use Database\Factories\List\ExternalProfileFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Class ExternalProfile.
 *
 * @property int $profile_id
 * @property Collection<int, ExternalEntry> $externalentries
 * @property ExternalToken|null $externaltoken
 * @property int|null $external_user_id
 * @property string $name
 * @property ExternalProfileSite $site
 * @property Carbon|null $synced_at
 * @property int|null $user_id
 * @property User|null $user
 * @property ExternalProfileVisibility $visibility
 *
 * @method static ExternalProfileFactory factory(...$parameters)
 */
class ExternalProfile extends BaseModel
{
    use Searchable;

    final public const TABLE = 'external_profiles';

    final public const ATTRIBUTE_ID = 'profile_id';
    final public const ATTRIBUTE_EXTERNAL_USER_ID = 'external_user_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_VISIBILITY = 'visibility';
    final public const ATTRIBUTE_USER = 'user_id';
    final public const ATTRIBUTE_SYNCED_AT = 'synced_at';

    final public const RELATION_ANIMES = 'externalentries.anime';
    final public const RELATION_EXTERNAL_ENTRIES = 'externalentries';
    final public const RELATION_EXTERNAL_TOKEN = 'externaltoken';
    final public const RELATION_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID,
        ExternalProfile::ATTRIBUTE_NAME,
        ExternalProfile::ATTRIBUTE_SITE,
        ExternalProfile::ATTRIBUTE_SYNCED_AT,
        ExternalProfile::ATTRIBUTE_VISIBILITY,
        ExternalProfile::ATTRIBUTE_USER,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ExternalProfileCreated::class,
        'deleted' => ExternalProfileDeleted::class,
        'restored' => ExternalProfileRestored::class,
        'updated' => ExternalProfileUpdated::class,
    ];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ExternalProfile::ATTRIBUTE_SITE => ExternalProfileSite::class,
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::class,
        ];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->user === null ? $this->getName() : $this->user->getName();
    }

    /**
     * Get the eager loads needed to the subtitle.
     *
     * @return array
     */
    public static function getEagerLoadsForSubtitle(): array
    {
        return [
            ExternalProfile::RELATION_USER,
        ];
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

    /**
     * Get the index name for the model when searching.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return 'profiles';
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
     * Check if the profile was created through username case.
     *
     * @return bool
     */
    public function isClaimed(): bool
    {
        return (bool) $this->user_id;
    }

    /**
     * Determine if the profile can be synced.
     *
     * @return bool
     */
    public function canBeSynced(): bool
    {
        return !$this->synced_at || $this->synced_at->addHours(3)->isPast();
    }

    /**
     * Dispatch the sync external profile job.
     *
     * @return void
     */
    public function startSyncJob(): void
    {
        SyncExternalProfileJob::dispatch($this);
    }

    /**
     * Get the client URL for the profile.
     * https://animethemes.moe/external/{mal|anilist}/{profile_name}
     *
     * @return string
     */
    public function getClientUrl(): string
    {
        return Str::of(Config::get('wiki.external_profile'))
            ->append('/')
            ->append(Str::lower($this->site->name))
            ->append('/')
            ->append($this->getName())
            ->__toString();
    }

    /**
     * Get the entries for the profile.
     *
     * @return HasMany<ExternalEntry, $this>
     */
    public function externalentries(): HasMany
    {
        return $this->hasMany(ExternalEntry::class, ExternalEntry::ATTRIBUTE_PROFILE);
    }

    /**
     * Get the user that owns the profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, ExternalProfile::ATTRIBUTE_USER);
    }

    /**
     * Get the external token that the external profile owns.
     *
     * @return HasOne<ExternalToken, $this>
     */
    public function externaltoken(): HasOne
    {
        return $this->hasOne(ExternalToken::class, ExternalToken::ATTRIBUTE_PROFILE);
    }

    /**
     * Get the prunable model query.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereDoesntHave(ExternalProfile::RELATION_USER)
            ->where(
                BaseModel::ATTRIBUTE_CREATED_AT,
                ComparisonOperator::LTE->value,
                Date::now()->subWeek()
            );
    }
}
